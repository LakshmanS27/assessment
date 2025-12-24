<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeywordsTableSeeder extends Seeder
{
    public function run(): void
    {
        $keywords = [
            'python',
            'java',
            'php',
            'laravel',
            'django',
            'machine learning',
            'fastapi',
            'docker',
            'tensorflow',
            'pytorch',
            'sql',
            'mongodb',
            'html',
            'css',
            'javascript',
        ];

        foreach ($keywords as $kw) {
    DB::table('keywords')->insert(['keyword' => $kw]);
    }   
}
}