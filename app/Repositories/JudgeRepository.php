<?php
namespace App\Repositories;

use Str;
use App\Models\User;
use App\Models\ScoreCard;
use App\Enums\UserRolesEnum;
use App\Helpers\UploadFiles;
use Illuminate\Support\Facades\DB;
use App\Mail\JudgeCredentialsEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Interfaces\Admin\JudgeInterface;

use function Laravel\Prompts\select;

class JudgeRepository implements JudgeInterface
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function index($contestId)
    {

     return        $this->model::where('contest_id', $contestId)
                             ->where('role', UserRolesEnum::JUDGE)
                             ->with('scorecards')
                             ->get();

    }

    public function show(string $id)
    {
        $judge = $this->model::findOrFail($id);
        return $judge;
    }

    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            $judgeDataArray = [];
            $judgeEmails = [];
            $judges = [];

            for ($i = 0; $i < count($data['judge_name']); $i++) {
                $randomPassword = $this->generateRandomComplexPassword(8);

                $judgeDataArray[] = [
                    'contest_id' => $data['contest_id'],
                    'name'       => $data['judge_name'][$i],
                    'email'      => $data['email'][$i],
                    'password'   => Hash::make($randomPassword),
                    'original_password' => $randomPassword,
                    'role'       => UserRolesEnum::JUDGE,
                    'admin_id'   => auth()->user()->id,
                    'profile_picture' => UploadFiles::upload($data['profile_picture'][$i], 'profile_picture', 'judge_picture'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $judgeEmails[$data['email'][$i]] = $randomPassword;
            }

            $this->model::insert($judgeDataArray);


            $createdJudges = $this->model::whereIn('email', array_keys($judgeEmails))->get();
            foreach ($createdJudges as $judge) {
                Mail::to($judge->email)->send(new JudgeCredentialsEmail($judge->name,$judge->email, $judgeEmails[$judge->email]));
                $judges[] = $judge;
            }

          ScoreCard::where('contest_id',$data['contest_id'])->delete();

            $scorecard = ScoreCard::create([
                'admin_id'    => auth()->user()->id,
                'contest_id'  => $data['contest_id'],
                'judge_id'    => $judges[0]->id,
                'fields'      => json_encode($data['fields']),
            ]);


            foreach ($judges as $judge) {
                $judge->scorecards()->attach($scorecard->id);
            }

            DB::commit();
            return response()->json(['message' => 'Judges and scorecard added successfully.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create judges and scorecard', 'error' => $e->getMessage()], 500);
        }
    }


    public function update(array $data, $id)
    {
        $judge = $this->model::findOrFail($id);
        $update = $judge->update([
            'name'       => $data['name'] ?? $judge->name,
            'password'   => isset($data['password']) ? Hash::make($data['password']) : $judge->password,
            'original_password' => $data['password'] ?? $judge->original_password,
            'profile_picture'   => isset($data['profile_picture']) ? UploadFiles::upload($data['profile_picture'], 'profile_picture', 'judge_picture') : $judge->profile_picture
        ]);
        return $judge;
    }

    public function destroy(string $id)
    {
        $judge = $this->model::findOrFail($id)->delete();
        return true;
    }

   private function generateRandomComplexPassword($length = 8)
{
    $upperCase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowerCase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $symbols = '!@#$%^&*()-_+=<>?';

    $allCharacters = $upperCase . $lowerCase . $numbers . $symbols;
    $password = '';

    // Ensure the password has at least one character from each set
    $password .= $upperCase[random_int(0, strlen($upperCase) - 1)];
    $password .= $lowerCase[random_int(0, strlen($lowerCase) - 1)];
    $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    $password .= $symbols[random_int(0, strlen($symbols) - 1)];

    // Fill the rest of the password length with random characters
    for ($i = 4; $i < $length; $i++) {
        $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
    }

    // Shuffle the password to ensure randomness
    return str_shuffle($password);
}
}
