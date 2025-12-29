<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminInviteController extends Controller
{
    // Invite a single user
    public function inviteSingle(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower(trim($request->email));

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => null,
                'role' => 'user',
                'is_invited' => true, // ✅ force invited
            ]
        );

        return back()->with('success', 'User invited successfully.');
    }

    // Invite multiple users via CSV
    public function inviteCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file));

        // Check header for valid_email
        $header = array_map('strtolower', array_map('trim', $csvData[0] ?? []));
        $emailIndex = array_search('valid_email', $header);

        if ($emailIndex === false) {
            return redirect()->back()->with('error', 'CSV must have a "valid_email" column.');
        }

        // Loop through rows, skip header
        for ($i = 1; $i < count($csvData); $i++) {
            $row = $csvData[$i];
            $email = strtolower(trim($row[$emailIndex] ?? ''));

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Force set is_invited = 1
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => null,
                        'role' => 'user',
                        'is_invited' => true,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'CSV processed successfully. Users invited.');
    }
}
