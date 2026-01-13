<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\ChatbotKeyword;
use App\Models\ChatbotAnalytics;
use App\Models\ChatbotFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ChatbotController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('Manage Chatbot')) {
            abort(403);
        }

        $totalConversations = ChatbotConversation::count();
        $totalFeedback = ChatbotFeedback::count();
        $helpfulFeedback = ChatbotFeedback::where('is_helpful', true)->count();
        $helpfulRate = $totalFeedback > 0 ? round(($helpfulFeedback / $totalFeedback) * 100) : 0;

        $topKeywords = ChatbotKeyword::orderBy('usage_count', 'desc')->limit(5)->get();

        $recentAnalytics = ChatbotAnalytics::with('conversation.user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.admin.chatbot.index', compact(
            'totalConversations',
            'totalFeedback',
            'helpfulRate',
            'topKeywords',
            'recentAnalytics'
        ));
    }

    public function keywords()
    {
        if (!auth()->user()->can('Manage Chatbot Keywords')) {
            abort(403);
        }

        $keywords = ChatbotKeyword::orderBy('keyword', 'asc')->paginate(15);
        return view('user.admin.chatbot.keywords', compact('keywords'));
    }

    public function keywordCreate()
    {
        return view('user.admin.chatbot.keyword-form');
    }

    public function keywordStore(Request $request)
    {
        $request->validate([
            'keyword' => 'required|unique:chatbot_keywords,keyword',
            'search_type' => 'required|in:others,estore,elearning',
            'response' => 'required',
        ]);

        ChatbotKeyword::create($request->all());
        return redirect()->route('user.admin.chatbot.keywords')->with('message', 'Keyword created successfully');
    }

    public function keywordEdit($id)
    {
        $keyword = ChatbotKeyword::findOrFail($id);
        return view('user.admin.chatbot.keyword-form', compact('keyword'));
    }

    public function keywordUpdate(Request $request, $id)
    {
        $keyword = ChatbotKeyword::findOrFail($id);
        $request->validate([
            'keyword' => 'required|unique:chatbot_keywords,keyword,' . $id,
            'search_type' => 'required|in:others,estore,elearning',
            'response' => 'required',
        ]);

        $keyword->update($request->all());
        return redirect()->route('user.admin.chatbot.keywords')->with('message', 'Keyword updated successfully');
    }

    public function keywordDelete($id)
    {
        $keyword = ChatbotKeyword::findOrFail($id);
        $keyword->delete();
        return redirect()->route('user.admin.chatbot.keywords')->with('message', 'Keyword deleted successfully');
    }

    public function keywordBulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('csv_file');

        try {
            $data = Excel::toArray([], $file);
            $rows = $data[0] ?? [];

            $count = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header row

                // Check if we have at least keyword and response
                if (!empty($row[0]) && !empty($row[1])) {
                    ChatbotKeyword::updateOrCreate(
                        ['keyword' => trim($row[0])],
                        [
                            'response' => trim($row[1]),
                            'search_type' => (!empty($row[2]) ? trim(strtolower($row[2])) : 'others'),
                            'is_active' => true
                        ]
                    );
                    $count++;
                }
            }

            return redirect()->route('user.admin.chatbot.keywords')->with('message', "$count keywords processed successfully");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['csv_file' => 'Error processing file: ' . $e->getMessage()]);
        }
    }

    public function keywordSampleDownload()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="chatbot_keywords_sample.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Keyword', 'Response', 'Search Type']);
            fputcsv($file, ['shipping, delivery', 'Our shipping usually takes 3-5 business days.', 'others']);
            fputcsv($file, ['apple watch, iphone', 'Search for latest gadgets in our store.', 'estore']);
            fputcsv($file, ['laravel course, react prep', 'Check out our specialized learning tracks.', 'elearning']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function conversations()
    {
        if (!auth()->user()->can('View Chatbot History')) {
            abort(403);
        }

        $conversations = ChatbotConversation::with(['user', 'messages'])
            ->withCount('messages')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.admin.chatbot.conversations', compact('conversations'));
    }

    public function conversationShow($id)
    {
        $conversation = ChatbotConversation::with(['user', 'messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('user.admin.chatbot.conversation-show', compact('conversation'));
    }
}
