<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Create repeatable demo accounts, categories, and sample books. */
    public function run(): void
    {
        foreach ([['Library Admin', 'admin@libtrack.test', 'admin'], ['Librarian', 'librarian@libtrack.test', 'librarian'], ['Demo Student', 'student@libtrack.test', 'student']] as [$name,$email,$role]) {
            User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => 'password', 'role' => $role]);
        }
        $cats = [];
        foreach (['Programming', 'Data Science', 'Networking'] as $name) {
            $cats[$name] = Category::firstOrCreate(['name' => $name]);
        }
        foreach ([['Programming', 'Laravel for Beginners', 'A. Developer'], ['Programming', 'Introduction to PHP', 'B. Coder'], ['Data Science', 'Practical Data Science', 'C. Analyst'], ['Networking', 'Computer Networks', 'D. Engineer']] as [$cat,$title,$author]) {
            Book::firstOrCreate(['title' => $title], ['category_id' => $cats[$cat]->id, 'author' => $author, 'description' => 'Sample book for classroom demonstration.', 'total_copies' => 5, 'available_copies' => 5]);
        }
    }
}
