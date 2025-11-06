<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Learn\Course\info\Course;

class CourseV2ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API v2 courses listing endpoint
     */
    public function test_api_v2_courses_index()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/v2/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'code',
                        'description',
                        'status',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
    }

    /**
     * Test API v2 course details endpoint
     */
    public function test_api_v2_courses_show()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/v2/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'code',
                    'description',
                    'status',
                    'settings',
                    'created_at',
                    'updated_at',
                ],
                'stats' => [
                    'total_groups',
                    'total_members',
                    'active_members',
                    'completion_rate',
                ]
            ]);
    }

    /**
     * Test API v2 course summary endpoint
     */
    public function test_api_v2_courses_summary()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/v2/courses/{$course->id}/summary");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'basic_info' => [
                        'id',
                        'name',
                        'code',
                        'category',
                        'level',
                        'status',
                        'enrolled_students',
                    ],
                    'statistics' => [
                        'total_groups',
                        'total_members',
                        'active_members',
                        'total_lessons',
                        'total_assignments',
                        'total_quizzes',
                    ],
                    'progress' => [
                        'average_completion',
                        'average_grade',
                    ],
                ]
            ]);
    }

    /**
     * Test API v2 course groups listing endpoint
     */
    public function test_api_v2_courses_groups_index()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/v2/courses/{$course->id}/groups");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'course_id',
                        'name',
                        'description',
                        'status',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
    }

    /**
     * Test API v2 course members listing endpoint
     */
    public function test_api_v2_courses_members_index()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/v2/courses/{$course->id}/members");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'course_id',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                        'member_name',
                        'role',
                        'status',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
    }

    /**
     * Test API v2 filtering functionality
     */
    public function test_api_v2_courses_filtering()
    {
        $user = User::factory()->create();
        Course::factory()->create([
            'user_id' => $user->id,
            'category' => 'programming',
            'level' => 'beginner'
        ]);
        Course::factory()->create([
            'user_id' => $user->id,
            'category' => 'design',
            'level' => 'advanced'
        ]);

        // Test category filter
        $response = $this->actingAs($user)
            ->getJson('/api/v2/courses?category=programming');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('programming', $data[0]['category']);

        // Test level filter
        $response = $this->actingAs($user)
            ->getJson('/api/v2/courses?level=advanced');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('advanced', $data[0]['level']);
    }

    /**
     * Test API v2 search functionality
     */
    public function test_api_v2_courses_search()
    {
        $user = User::factory()->create();
        Course::factory()->create([
            'user_id' => $user->id,
            'name' => 'Laravel Development'
        ]);
        Course::factory()->create([
            'user_id' => $user->id,
            'name' => 'Vue.js Fundamentals'
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v2/courses?search=Laravel');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Laravel', $data[0]['name']);
    }
}