<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'search_type',
        'response',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Increment usage count.
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Search for matching keyword.
     */
    public static function findByKeyword($query)
    {
        $query = strtolower($query);

        // Find all active keywords
        $allKeywords = self::where('is_active', true)->get();

        $match = null;
        $maxLen = 0;

        foreach ($allKeywords as $kw) {
            // Support comma-separated aliases in a single keyword field
            $aliases = explode(',', $kw->keyword);

            foreach ($aliases as $alias) {
                $aliasText = trim(strtolower($alias));
                if (empty($aliasText)) continue;

                // Use word boundaries for precise matching
                $pattern = '/\b' . preg_quote($aliasText, '/') . '\b/i';

                if (preg_match($pattern, $query)) {
                    // We want the longest match found across any keyword/alias
                    if (strlen($aliasText) > $maxLen) {
                        $maxLen = strlen($aliasText);
                        $match = $kw;
                    }
                }
            }
        }

        return $match;
    }
}
