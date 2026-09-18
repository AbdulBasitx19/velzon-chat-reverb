<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\MessageAttachment;
use App\Events\MessageSent;
use App\Events\FileSent;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    //
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();

        return view('chat.index', compact('users'));
    }

    public function store(Request $request){
        $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request['receiver_id'],
            'message' => $request['message'],
        ]);

        event(new MessageSent($message)); // Event fire kiya 

        // ajax k liay response
        return response()->json([
            'success' => 'true',
            'message' => $message->load('sender'), // Sender ki details bhej di
        ]);
    }

    public function getMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId)
        {
            $query->where('sender_id', auth()->id())->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId){
            $query->where('sender_id', $userId)->where('receiver_id', auth()->id());
        })->with('sender', 'attachments') // Sender ki details bhi load karo Attachments bhi load karo
        ->orderBy('created_at', 'asc') // Purane messages pehle
        ->get();

        return response()->json($messages);
    }

    public function sendFile(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000',
            'files' => 'required|array|max:5', // Max 5 files
            'files.*' => 'required|file|max:51200', // Max 50MB per file (in KB)
        ]);

        // 2. Message create karo (agar text hai ya sirf files ke liye empty message)
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message ?? '', // Agar text nahi hai toh empty string
        ]);

        // 3. Files ko process karo
        $attachments = [];
        
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // File category determine karo
                $mimeType = $file->getMimeType();
                $category = $this->determineCategory($mimeType);
                
                // Storage disk determine karo (Public vs Private)
                $disk = $this->determineDisk($category);
                
                // File save karo
                $path = $file->store('chat_files', $disk);
                
                // Attachment create karo
                $attachment = MessageAttachment::create([
                    'message_id' => $message->id,
                    'user_id' => auth()->id(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $mimeType,
                    'file_category' => $category, // 'image', 'video', 'document'
                    'file_size' => $file->getSize(), // Bytes mein
                    'thumbnail_path' => null, // Future mein thumbnail generate kar sakte hain
                ]);
                
                $attachments[] = $attachment;
            }
        }
        // 4. Message ko attachments ke sath load karo
        $message->load('sender', 'attachments');

        // 5. Event fire karo (FileSent)
        event(new FileSent($message));

        // 6. Response return karo
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    // File category determine karna
    private function determineCategory($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } else {
            return 'document';
        }
    }

    //  Storage disk determine (Public vs Private)
    private function determineDisk($category)
    {
        // Images aur Videos PUBLIC mein jayengi (fast rendering ke liye)
        if (in_array($category, ['image', 'video'])) {
            return 'public';
        }
        // Documents PRIVATE mein jayengi (security ke liye)
        else {
            return 'local'; // Ya aap custom 'private' disk bhi bana sakte hain
        }
    }

    // File download  (Private files ke liye)
    public function download($attachmentId)
    {
        $attachment = MessageAttachment::findOrFail($attachmentId);
        
        // Security check: Kya current user is message ka participant hai?
        $message = $attachment->message;
        if ($message->sender_id != auth()->id() && $message->receiver_id != auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        // File path determine karo
        $disk = $this->determineDisk($attachment->file_category);
        $path = $attachment->file_path;
        
        // Agar file public disk mein hai, toh direct URL redirect karo
        if ($disk === 'public') {
            return redirect(Storage::disk('public')->url($path));
        }
        
        // Agar file private disk mein hai, toh download response bhejo
        return Storage::disk($disk)->download($path, $attachment->file_name);
    }

    //  Message delete karna
    public function destroy($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // Security check: Kya current user is message ka sender hai?
        if ($message->sender_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        // Attachments ki files delete karo (Storage se)
        foreach ($message->attachments as $attachment) {
            $disk = $this->determineDisk($attachment->file_category);
            Storage::disk($disk)->delete($attachment->file_path);
            
            // Thumbnail bhi delete karo agar hai
            if ($attachment->thumbnail_path) {
                Storage::disk($disk)->delete($attachment->thumbnail_path);
            }
        }
        
        // Message delete karo (cascade se attachments bhi delete ho jayengi)
        $message->delete();
        
        return response()->json(['success' => true]);
    }
}