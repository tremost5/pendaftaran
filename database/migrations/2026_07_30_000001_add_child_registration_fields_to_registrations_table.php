<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'nickname')) {
                $table->string('nickname')->after('full_name');
            }

            if (! Schema::hasColumn('registrations', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('nickname');
            }

            if (! Schema::hasColumn('registrations', 'home_address')) {
                $table->text('home_address')->after('date_of_birth');
            }

            if (! Schema::hasColumn('registrations', 'school_origin')) {
                $table->string('school_origin')->after('home_address');
            }

            if (! Schema::hasColumn('registrations', 'school_class')) {
                $table->string('school_class')->after('school_origin');
            }

            if (! Schema::hasColumn('registrations', 'gender')) {
                $table->string('gender')->after('school_class');
            }

            if (! Schema::hasColumn('registrations', 'service_interests')) {
                $table->json('service_interests')->nullable()->after('gender');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'nickname',
                'date_of_birth',
                'home_address',
                'school_origin',
                'school_class',
                'gender',
                'service_interests',
            ]);
        });
    }
};