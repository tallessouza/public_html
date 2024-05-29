<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Usage extends Model
{
	protected $table = 'usage';
	protected $fillable = [
		'total_user_count',
		'this_week_user_count',
		'last_week_user_count',
		'total_word_count',
        'this_week_word_count',
        'last_week_word_count',
        'total_image_count',
        'this_week_image_count',
        'last_week_image_count'
	];

	public static function getSingle()
    {
        return static::firstOrCreate([]);
    }

    // Define method to update word count
    public function updateWordCounts($count)
    {
        $this->total_word_count += $count;
        $this->this_week_word_count += $count;

        // Check if a week has passed since the last update
        if ($this->updated_at && Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
            // Move this week counts to last week counts
            $this->last_week_word_count = $this->this_week_word_count;
            $this->this_week_word_count = 0;
        }

        $this->save();
    }

    // Define method to update image count
    public function updateImageCounts($count)
    {
        $this->total_image_count += $count;
        $this->this_week_image_count += $count;

        // Check if a week has passed since the last update
        if ($this->updated_at && Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
            // Move this week counts to last week counts
            $this->last_week_image_count = $this->this_week_image_count;
            $this->this_week_image_count = 0;
        }

        $this->save();
    }

	// Define method to update user count
	public function updateUserCount($count)
	{
		$this->total_user_count += $count;
		$this->this_week_user_count += $count;

		// Check if a week has passed since the last update
		if ($this->updated_at && Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
			// Move this week counts to last week counts
			$this->last_week_user_count = $this->this_week_user_count;
			$this->this_week_user_count = 0;
		}

		$this->save();
	}

	// Define method to update sales count
	public function updateSalesCount($count)
	{
		$this->total_sales += $count;
		$this->this_week_sales += $count;

		// Check if a week has passed since the last update
		if ($this->updated_at && Carbon::now()->diffInWeeks($this->updated_at) >= 1) {
			// Move this week counts to last week counts
			$this->last_week_sales = $this->this_week_sales;
			$this->this_week_sales = 0;
		}

		$this->save();
	}

    // Override update method to update counts
    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);
        // Update counts
        $this->updateCounts();
    }

    // Method to update counts after create or update
    protected function updateCounts()
    {
        // Get the current week number
        $currentWeek = Carbon::now()->weekOfYear;

        // Check if the last update week is different from the current week
        if (!$this->updated_at || $this->updated_at->weekOfYear != $currentWeek) {
            // Move this week counts to last week counts
            $this->last_week_word_count = $this->this_week_word_count;
            $this->last_week_image_count = $this->this_week_image_count;
			$this->last_week_user_count = $this->this_week_user_count;
			$this->last_week_sales = $this->this_week_sales;

			// Reset this week counts
            $this->this_week_word_count = 0;
            $this->this_week_image_count = 0;
			$this->this_week_user_count = 0;
			$this->this_week_sales = 0;
        }

        // Update the total counts
        $this->total_word_count = $this->last_week_word_count + $this->this_week_word_count;
        $this->total_image_count = $this->last_week_image_count + $this->this_week_image_count;
		$this->total_user_count = $this->last_week_user_count + $this->this_week_user_count;
		$this->total_sales = $this->last_week_sales + $this->this_week_sales;

        $this->save();
    }
}