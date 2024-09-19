<?php

namespace App\Interfaces\Admin;
use App\Models\ScoreCard;
use Illuminate\Database\Eloquent\Collection;

interface ScoreCardInterface
{
public function getAll($contestId);

public function findById(int $id): ?ScoreCard;

public function create(array $data): ScoreCard;

public function update(int $id, array $data): bool;

public function delete(int $id): bool;

public function judgeScoreCard($contestId);
}
