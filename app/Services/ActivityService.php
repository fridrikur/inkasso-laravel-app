<?php 

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ActivityService
{
    public function log(
        string $description,
        ?Model $subject = null,
        array $properties = []
    ): void {

        $activity = activity();

        if ($subject) {
            $activity->performedOn($subject);
        }

        if (auth()->check()) {
            $activity->causedBy(auth()->user());
        }

        if (!empty($properties)) {
            $activity->withProperties($properties);
        }

        $activity->log($description);
    }
}