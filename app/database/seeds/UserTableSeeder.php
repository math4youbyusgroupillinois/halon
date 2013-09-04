class UserTableSeeder extends Seeder {
    public function run(){
        DB::table('users')->delete();

        User::create(array(
            'role' => 'admin',
            'password' => Hash::make('admin')
        ));
    }
}