<?php

namespace App\Http\Controllers\Play\Game;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\MentalMathScore;
use Carbon\Carbon;

class MentalMathController extends Controller
{
    /**
     * Display the mental math game page.
     */
    public function index()
    {
        return Inertia::render('Play/Games/MentalMath/MentalMath');
    }

    /**
     * Generate a new math question based on difficulty.
     */
    public function generateQuestion(Request $request): JsonResponse
    {
        $difficulty = $request->input('difficulty', 'easy');
        $isDailyChallenge = $request->boolean('daily_challenge', false);
        
        $question = $this->createQuestion($difficulty, $isDailyChallenge);
        
        return response()->json($question);
    }

    /**
     * Save the player's score.
     */
    public function saveScore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_name' => 'required|string|max:255',
            'score' => 'required|integer|min:0',
            'difficulty' => 'required|in:easy,medium,hard',
            'combo' => 'required|integer|min:0',
            'questions_answered' => 'required|integer|min:0',
            'accuracy' => 'required|numeric|min:0|max:100',
            'time_spent' => 'required|integer|min:0',
            'is_practice_mode' => 'boolean',
            'is_daily_challenge' => 'boolean',
        ]);

        $score = MentalMathScore::create([
            'user_id' => auth()->id(),
            'player_name' => $validated['player_name'],
            'score' => $validated['score'],
            'difficulty' => $validated['difficulty'],
            'combo' => $validated['combo'],
            'questions_answered' => $validated['questions_answered'],
            'accuracy' => $validated['accuracy'],
            'time_spent' => $validated['time_spent'],
            'is_practice_mode' => $validated['is_practice_mode'] ?? false,
            'is_daily_challenge' => $validated['is_daily_challenge'] ?? false,
            'date_played' => now()->toDateString(),
        ]);

        return response()->json(['success' => true, 'score_id' => $score->id]);
    }

    /**
     * Get the leaderboard.
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        $difficulty = $request->input('difficulty', 'all');
        $limit = $request->input('limit', 10);
        
        $query = MentalMathScore::notPracticeMode();
        
        if ($difficulty !== 'all') {
            $query->byDifficulty($difficulty);
        }
        
        $leaderboard = $query->with('user:id,name')
            ->topScores($limit)
            ->get()
            ->map(function ($score) {
                return [
                    'id' => $score->id,
                    'player_name' => $score->player_name,
                    'score' => $score->score,
                    'difficulty' => $score->difficulty,
                    'combo' => $score->combo,
                    'accuracy' => $score->accuracy,
                    'date_played' => $score->date_played,
                    'created_at' => $score->created_at->diffForHumans(),
                ];
            });

        return response()->json($leaderboard);
    }

    /**
     * Get player statistics.
     */
    public function getPlayerStats(Request $request): JsonResponse
    {
        $playerName = $request->input('player_name');
        
        $stats = MentalMathScore::where('player_name', $playerName)
            ->notPracticeMode()
            ->get();

        $totalGames = $stats->count();
        $totalScore = $stats->sum('score');
        $averageScore = $totalGames > 0 ? round($totalScore / $totalGames) : 0;
        $highestScore = $stats->max('score') ?? 0;
        $highestCombo = $stats->max('combo') ?? 0;
        $averageAccuracy = $totalGames > 0 ? round($stats->avg('accuracy'), 2) : 0;
        $totalQuestions = $stats->sum('questions_answered');
        $totalTime = $stats->sum('time_spent');

        $bestDifficulty = $stats->groupBy('difficulty')
            ->map(function ($group) {
                return $group->max('score');
            })
            ->sortDesc()
            ->keys()
            ->first();

        return response()->json([
            'total_games' => $totalGames,
            'total_score' => $totalScore,
            'average_score' => $averageScore,
            'highest_score' => $highestScore,
            'highest_combo' => $highestCombo,
            'average_accuracy' => $averageAccuracy,
            'total_questions' => $totalQuestions,
            'total_time' => $totalTime,
            'best_difficulty' => $bestDifficulty,
        ]);
    }

    /**
     * Get daily challenge question.
     */
    public function getDailyChallenge(): JsonResponse
    {
        $today = now()->toDateString();
        $seed = crc32($today); // Create a seed based on today's date
        mt_srand($seed);
        
        $question = $this->createQuestion('medium', true);
        
        // Reset the random seed
        mt_srand();
        
        return response()->json($question);
    }

    /**
     * Create a math question based on difficulty.
     */
    private function createQuestion(string $difficulty, bool $isDailyChallenge = false): array
    {
        $operators = ['+', '-', '*', '/'];
        $operator = $operators[array_rand($operators)];
        
        switch ($difficulty) {
            case 'easy':
                $min = 1;
                $max = 9;
                $timeLimit = 10;
                break;
            case 'medium':
                $min = 10;
                $max = 99;
                $timeLimit = 7;
                break;
            case 'hard':
                $min = 100;
                $max = 999;
                $timeLimit = 5;
                break;
            default:
                $min = 1;
                $max = 9;
                $timeLimit = 10;
        }
        
        $num1 = mt_rand($min, $max);
        $num2 = mt_rand($min, $max);
        
        // Handle division to ensure whole numbers
        if ($operator === '/') {
            $num1 = $num2 * mt_rand(1, 10);
        }
        
        // Handle subtraction to avoid negative results
        if ($operator === '-' && $num1 < $num2) {
            [$num1, $num2] = [$num2, $num1];
        }
        
        $question = "{$num1} {$operator} {$num2}";
        $answer = eval("return {$question};");
        
        // Add bonus points for daily challenge
        $bonusPoints = $isDailyChallenge ? 50 : 0;
        
        return [
            'question' => $question,
            'answer' => $answer,
            'difficulty' => $difficulty,
            'time_limit' => $timeLimit,
            'bonus_points' => $bonusPoints,
            'is_daily_challenge' => $isDailyChallenge,
        ];
    }
}
