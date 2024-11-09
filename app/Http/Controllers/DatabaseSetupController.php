<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DatabaseSetupController extends Controller
{
    public function showSetupPage()
    {
        return view('setup');
    }

    public function createDatabase()
    {
        try {
            DB::statement('CREATE DATABASE IF NOT EXISTS vuln_db');
            DB::statement('USE vuln_db');
            $this->createTables();
            return redirect()->back()->with('success', 'Database and tables created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function restoreDatabase()
    {
        try {
            DB::statement('DROP DATABASE IF EXISTS vuln_db');
            DB::statement('CREATE DATABASE vuln_db');
            DB::statement('USE vuln_db');
            $this->createTables();
            return redirect()->back()->with('success', 'Database restored successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function createTables()
    {
        DB::statement(
            'CREATE TABLE IF NOT EXISTS books (
            number INT NOT NULL,
            bookname VARCHAR(50) NOT NULL,
            authorname VARCHAR(50) NOT NULL
        )'
        );

        DB::statement(
            'CREATE TABLE IF NOT EXISTS flags (
            flag VARCHAR(50) NOT NULL
        )'
        );

        DB::statement(
            'CREATE TABLE IF NOT EXISTS secret (
            username VARCHAR(56) NOT NULL,
            password VARCHAR(56) NOT NULL
        )'
        );

        DB::statement(
            'CREATE TABLE IF NOT EXISTS users (
            firstname VARCHAR(50) NOT NULL,
            lastname VARCHAR(50) NOT NULL,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(50) NOT NULL
        )'
        );
    }
}
