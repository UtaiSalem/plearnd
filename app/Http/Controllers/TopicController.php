<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Resources\TopicResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\AcademyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\AssignmentResource;

class TopicController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => ['required'],
            'lesson_id' => ['required'],
            'title'     => ['required', 'string'],
            'content'   => ['required', 'string'],
        ]);

        $topic = Topic::create([
            'user_id' => auth()->user()->id,
            'course_id' => $validatedData['course_id'],
            'lesson_id' => $validatedData['lesson_id'],
            'title'   => $validatedData['title'],
            'content' => $validatedData['content'],
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            // $fileNames = [];
            foreach ($images as $image) {
                // $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                // $image_url = Storage::disk('public')->putFileAs('images/topics', $image, $fileName);
                // $file_path = $request->file('cover')->storeAs('logos', $file_name, 'public');
                $image_url = $image->store('images/topics', 'public');

                // $fileNames[] = $fileName;

                $topic->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'newTopic' => new TopicResource(Topic::find($topic->id)),
        ], 200);
    }

    public function show(Topic $topic)
    {
        $lesson = $topic->lesson;
        $course = $lesson->course;
        $academy = $course->academy;

        if ($topic->user_id == auth()->id()) {
            $isCourseAdmin = true;
        }else{
            $isCourseAdmin = false;
        }

        return Inertia::render('Topic',[
            'isCourseAdmin' => $isCourseAdmin,
            'academy'       => new AcademyResource($academy),
            'course'        => new CourseResource($lesson->course),
            'lesson'        => new LessonResource($lesson),
            'topic'         => new TopicResource($topic),
            'assignments'   => AssignmentResource::collection($topic->assignments),
            'questions'     => QuestionResource::collection($topic->questions),
            'imagePath'     => '/../../'
        ]);
    }

    public function edit(Topic $topic)
    {
        // $lesson = $topic->lesson;
        // $course = $lesson->course;

        // return Inertia::render('Topic',[
        //     'course' => $course,
        //     'lesson' => $lesson,
        //     'topic' => new TopicResourse($topic),
        //     'profilePath' => '../../'
        // ]);
    }

    public function update(Topic $topic, Request $request,)
    {

        // if ($request->hasFile('images')) {
        //     $images = $request->image('images');
        //     foreach ($images as $image) {
        //         $path = Storage::putFile('/public/images', $image);
        //         $url = Storage::url($path);
        //     }
        // }
        // $tmpPath=[];
        // $files = $request->file('images');

        // foreach ($files as $file) {
        //     $path = Storage::disk('public')->put($file->getClientOriginalName(), $file);
        //     $tmpPath[$path] = $path;
        // }

        // if($request->hasFile('images')) {
        //     $files = $request->file('images');
        //     foreach ($files as $file) {
                // if($academy->logo && ($academy->logo !== 'logos/default_logo.png')){
                //     Storage::disk('public')->delete($academy->logo);
                // };
                // $file_name = time().'_'.$file->getClientOriginalName();
                // $file_path = $file->storeAs('topics', $file_name, 'public');
                // $academy->logo = $logo_path;
        //         $path = Storage::disk('public/topics')->put($file->getClientOriginalName(), $file);
        //     }

        // }

        // $image_get = $request->images;
        // foreach ($image_get as $image) {
            // $fileOriginalName = $image->getClientOriginalExtension();
            // $fileNewName = time() .'_'. $fileOriginalName;
            // $image->storeAs('images', $fileNewName, 'public'); //here images is folder, $fileNewName is files new name, public indicated public folder. that means folder this image in public/storage/images folder
            // $path = Storage::disk('public')->put($fileNewName, $image);
            // $path = Storage::putFile('public/storage/topics', $image);
            // Gallery::create([
            //     'album' => $request->album,
            //     'image' => $fileNewName
            // ]);
        // }

        $topic->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/topics', $image, $fileName);
                $fileNames[] = $fileName;

                $topicImages[] = $topic->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }

        // return to_route('topics.show', $topic->id);
        return response()->json([
            'topic' => new TopicResource($topic),
            'images' => $topicImages ?? []
        ], 200);
    }

    public function destroy(Topic $topic)
    {
        if ($topic->images) {
            foreach ($topic->images as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();   
            }
        }

        if ($topic->assignments) {
            foreach ($topic->assignments as $assignment) {
                if ($assignment->images) {
                    foreach ($assignment->images as $image) {
                        Storage::disk('public')->delete($image->image_url);
                        $image->delete();   
                    }
                }
                if ($assignment->answers) {
                    foreach ($assignment->answers as $answer) {
                        if ($answer->images) {
                            foreach ($answer->images as $image) {
                                Storage::disk('public')->delete($image->image_url);
                                $image->delete();   
                            }
                        }
                        $answer->delete();   
                    }
                }
    
                $assignment->delete();   
            }
        }

        if ($topic->questions) {
            foreach ($topic->questions as $question) {
                if ($question->images) {
                    foreach ($question->images as $image) {
                        Storage::disk('public')->delete($image->image_url);
                        $image->delete();   
                    }
                }
                
                if($question->answers){
                    foreach ($question->answers as $answer) {
                        if ($answer->images) {
                            foreach ($answer->images as $image) {
                                Storage::disk('public')->delete($image->image_url);
                                $image->delete();   
                            }
                        }
                        $answer->delete();   
                    }
                }

                $question->delete();   
            }
        }


        $topic->delete();
        
        return response()->json([
            'success' => true
        ], 200);
    }

    public function assignmentsStore(Topic $topic, Request $request)
    {
        $assignment = $topic->assignments()->create([
            'title' => $request->title,
            'points' => $request->points,
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/topics/assignments', $image, $fileName);
                $fileNames[] = $fileName;

                $assignment->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }
        return response()->json([
            'assignment' => new AssignmentResource($assignment),
        ], 200);
    }
}
