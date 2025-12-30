<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminInviteController extends Controller
{
    // Invite a single user
    public function inviteSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'singleInvite')
                ->withInput()
                ->with('active_tab', 'single'); // Keep Single Invite tab active
        }

        $email = strtolower(trim($request->email));

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => "Invited User",
                'role' => 'user',
                'is_invited' => true,
            ]
        );

        if ($user->wasRecentlyCreated) {
            return redirect()->back()
                ->with('success', 'User invited successfully.')
                ->with('active_tab', 'single');
        } else {
            return redirect()->back()
                ->with('info', 'User already exists and has been invited.')
                ->with('active_tab', 'single');
        }
    }

    // Invite multiple users via CSV
    public function inviteCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'csvInvite')
                ->withInput()
                ->with('active_tab', 'csv'); // Keep CSV tab active
        }

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file));

        $header = array_map('strtolower', array_map('trim', $csvData[0] ?? []));
        $emailIndex = array_search('valid_email', $header);

        if ($emailIndex === false) {
            return redirect()->back()
                ->with('error', 'CSV must have a "valid_email" column.')
                ->with('active_tab', 'csv');
        }

        $alreadyExists = [];
        $invited = [];

        for ($i = 1; $i < count($csvData); $i++) {
            $row = $csvData[$i];
            $email = strtolower(trim($row[$emailIndex] ?? ''));

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => "Invited User",
                        'role' => 'user',
                        'is_invited' => true,
                    ]
                );

                if ($user->wasRecentlyCreated) {
                    $invited[] = $email;
                } else {
                    $alreadyExists[] = $email;
                }
            }
        }

        $message = 'CSV processed successfully. ';
        if ($invited) $message .= count($invited) . ' new users invited. ';
        if ($alreadyExists) $message .= count($alreadyExists) . ' users already existed.';

        return redirect()->back()
            ->with('success', $message)
            ->with('active_tab', 'csv'); // Keep CSV tab active
    }
}
