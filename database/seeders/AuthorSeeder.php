<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Author::create(['first_name' => 'Oda', 'last_name' => 'Eiichiro']);
        Author::create(['first_name' => 'Tabata', 'last_name' => 'Yuki']);
        Author::create(['first_name' => 'Murata', 'last_name' => 'Yusuke']);
        Author::create(['first_name' => 'Kaneshiro', 'last_name' => 'Muneyuki']);
        Author::create(['first_name' => 'Matsumoto', 'last_name' => 'Naoya']);
        Author::create(['first_name' => 'Gotouge', 'last_name' => 'Koyoharu']);
    }
}
