<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

/**
 * @group Jobs
 * 
 * @authenticated
 */

class JobController extends Controller
{
    /**
     * All Job Posts
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "created_by": 2,
     *             "job_title": "Software Engineer",
     *             "job_description": "<p>Develop and maintain software applications.<p>",
     *             "job_type": "Full-time",
     *             "job_location": "Remote",
     *             "job_salary": "80,000 - 100,000",
     *             "job_experience": "3+ years",
     *             "contact_person": "John Doe",
     *             "contact_email": "johndoe@example.com",
     *             "list_of_values": "Team player, Good communication",
     *             "created_at": "2024-11-08T12:00:00.000000Z",
     *             "updated_at": "2024-11-08T12:00:00.000000Z",
     *             "user": {
     *                 "id": 2,
     *                 "name": "John Doe",
     *                 "email": "john@example.com"
     *             }
     *         },
     *         {
     *             "id": 2,
     *             "created_by": 3,
     *             "job_title": "Project Manager",
     *             "job_description": "Manage project timelines and deliverables.",
     *             "job_type": "Contract",
     *             "job_location": "On-site",
     *             "job_salary": "60,000 - 80,000",
     *             "job_experience": "5+ years",
     *             "contact_person": "Jane Smith",
     *             "contact_email": "janesmith@example.com",
     *             "list_of_values": "Leadership, Organizational skills",
     *             "created_at": "2024-11-08T12:00:00.000000Z",
     *             "updated_at": "2024-11-08T12:00:00.000000Z",
     *             "user": {
     *                 "id": 3,
     *                 "name": "Jane Smith",
     *                 "email": "jane@example.com"
     *             }
     *         }
     *     ]
     * }
     * @response 201 {
     *     "error": "Failed to load jobs."
     * }
     */
    public function index()
    {
        try {
            $jobs = Job::with('user')->get();

            return response()->json(['data' => $jobs], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load jobs.'], 201);
        }
    }


    /**
     * Single Job Details
     *
     * @urlParam id int required The ID of the job. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "created_by": 2,
     *         "job_title": "Software Engineer",
     *         "job_description": "<p><strong>Python </strong>is an interpreted, interactive, object-oriented programming language. It incorporates modules, dynamic typing, dynamic data types and classes. It's also portable, extensible and easy to read and code, which is a big reason it's popular.The language also focuses on designer experience and usability, and one of its largest benefits is that it has functions in many industries and organizations. You can integrate it with<strong> C</strong> and <strong>C++</strong> for more challenging tasks. Common uses of Python include:</p><ul><li>Data visualization</li><li>Artificial intelligence and machine learning</li><li>Finance</li><li>Game development</li><li>Web development</li></ul>",
     *         "job_type": "Full-time",
     *         "job_location": "Remote",
     *         "job_salary": "80,000 - 100,000",
     *         "job_experience": "3+ years",
     *         "contact_person": "John Doe",
     *         "contact_email": "johndoe@example.com",
     *         "list_of_values": "Team player, Good communication",
     *         "created_at": "2024-11-08T12:00:00.000000Z",
     *         "updated_at": "2024-11-08T12:00:00.000000Z",
     *         "user": {
     *             "id": 2,
     *             "name": "John Doe",
     *             "email": "john@example.com"
     *         }
     *     }
     * }
     * @response 404 {
     *     "error": "Job not found."
     * }
     */
    public function show($id)
    {
        try {
            $job = Job::with('user')->findOrFail($id);

            return response()->json(['data' => $job], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load job details.'], 201);
        }
    }


}
