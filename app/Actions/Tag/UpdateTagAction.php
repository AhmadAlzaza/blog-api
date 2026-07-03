<?php

namespace App\Actions\Tag;

use App\Models\Tag;

class UpdateTagAction
{
    public function execute(Tag $tag, string $newName): Tag
    {
        $tag->update(['name' => $newName]);
        return $tag->fresh();
    }
}
