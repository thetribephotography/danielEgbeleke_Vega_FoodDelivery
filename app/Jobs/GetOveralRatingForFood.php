<?php

namespace App\Jobs;

use App\Models\Comment;
use App\Models\Food;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GetOveralRatingForFood implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $comments = Comment::where('food_id', $this->id)->whereNotNull('rating')->get();

        $total_rating = $comments->sum('rating');
        $total_comments = $comments->count();

        if($total_comments > 0){
            $overal_rating = $total_rating / $total_comments;
            $food = Food::find($this->id);
            $food->update([
                'overal_rating' => $overal_rating
            ]);
        }

    }
}
