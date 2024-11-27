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


    /**
     * Create Job
     *
     * @bodyParam job_title string required The title of the job. Example: Software Engineer
     * @bodyParam job_description string required The description of the job. Example: A great job opportunity
     * @bodyParam job_type string required The type of the job (e.g., full-time, part-time). Example: full-time
     * @bodyParam job_location string required The location of the job. Example: New York
     * @bodyParam job_salary numeric optional The salary for the job. Example: 50000
     * @bodyParam list_of_values string optional List of additional job requirements or values. Example: hourly
     * @bodyParam currency string optional The currency for the salary. Example: USD
     * @bodyParam job_experience numeric optional The minimum experience required for the job. Example: 3
     * @bodyParam contact_person string optional The contact person for the job. Example: John Doe
     * @bodyParam contact_email string optional The contact email for the job. Example: johndoe@example.com
     *
     * @response 200 {
     *   "message": "Job has been created successfully.",
     *   "job": {
     *     "id": 1,
     *     "job_title": "Software Engineer",
     *     "job_description": "A great job opportunity",
     *     "job_type": "full-time",
     *     "job_location": "New York",
     *     "job_salary": 50000,
     *     "currency": "USD",
     *     "job_experience": 3,
     *     "contact_person": "John Doe",
     *     "contact_email": "johndoe@example.com",
     *     "list_of_values": "hourly",
     *     "created_by": 1,
     *     "created_at": "2024-11-27T00:00:00.000000Z",
     *     "updated_at": "2024-11-27T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "job_title": ["The job title is required."],
     *     "job_description": ["The job description is required."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'job_title' => 'required',
                'job_description' => 'required',
                'job_type' => 'required',
                'job_location' => 'required',
                'job_salary' => 'nullable|numeric',
                'currency' => 'nullable',
                'job_experience' => 'nullable|numeric',
                'contact_person' => 'nullable',
                'contact_email' => 'nullable|email',
                'list_of_values' => 'nullable',
            ]);

            // Create a new job entry
            $job = new Job();
            $job->created_by = auth()->id();
            $job->job_title = $request->job_title;
            $job->job_description = $request->job_description;
            $job->job_type = $request->job_type;
            $job->job_location = $request->job_location;
            $job->job_salary = $request->job_salary;
            $job->currency = $request->currency;
            $job->job_experience = $request->job_experience;
            $job->contact_person = $request->contact_person;
            $job->contact_email = $request->contact_email;
            $job->list_of_values = $request->list_of_values;
            $job->save();

            // Return success response
            return response()->json([
                'message' => 'Job has been created successfully.',
                'job' => $job
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 201);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An error occurred while creating the job.',
            ], 201);
        }
    }


    /**
     * Update Job
     *
     * @bodyParam job_title string required The title of the job. Example: Software Engineer
     * @bodyParam job_description string required The description of the job. Example: A great job opportunity
     * @bodyParam job_type string required The type of the job (e.g., full-time, part-time). Example: full-time
     * @bodyParam job_location string required The location of the job. Example: New York
     * @bodyParam job_salary numeric optional The salary for the job. Example: 50000
     * @bodyParam list_of_values string optional List of additional job requirements or values. Example: hourly
     * @bodyParam currency string optional The currency for the salary. Example: USD
     * @bodyParam job_experience numeric optional The minimum experience required for the job. Example: 3
     * @bodyParam contact_person string optional The contact person for the job. Example: John Doe
     * @bodyParam contact_email string optional The contact email for the job. Example: johndoe@example.com
     *
     * @response 200 {
     *   "message": "Job has been updated successfully.",
     *   "job": {
     *     "id": 1,
     *     "job_title": "Software Engineer",
     *     "job_description": "A great job opportunity",
     *     "job_type": "full-time",
     *     "job_location": "New York",
     *     "job_salary": 50000,
     *     "currency": "USD",
     *     "job_experience": 3,
     *     "contact_person": "John Doe",
     *     "contact_email": "johndoe@example.com",
     *     "list_of_values": "hourly"
     *     "created_by": 1,
     *     "created_at": "2024-11-27T00:00:00.000000Z",
     *     "updated_at": "2024-11-27T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "job_title": ["The job title is required."],
     *     "job_description": ["The job description is required."]
     *   }
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'job_title' => 'required',
                'job_description' => 'required',
                'job_type' => 'required',
                'job_location' => 'required',
                'job_salary' => 'nullable|numeric',
                'job_experience' => 'nullable|numeric',
                'contact_person' => 'nullable',
                'contact_email' => 'nullable|email',
                'currency' => 'nullable',
                'list_of_values' => 'nullable',
            ]);

            // Find the job by ID
            $job = Job::findOrFail($id);

            // Update the job details
            $job->job_title = $request->job_title;
            $job->job_description = $request->job_description;
            $job->job_type = $request->job_type;
            $job->job_location = $request->job_location;
            $job->job_salary = $request->job_salary;
            $job->currency = $request->currency;
            $job->job_experience = $request->job_experience;
            $job->contact_person = $request->contact_person;
            $job->contact_email = $request->contact_email;
            $job->list_of_values = $request->list_of_values;
            $job->save();

            // Return success response
            return response()->json([
                'message' => 'Job has been updated successfully.',
                'job' => $job
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 201);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An error occurred while updating the job.',
            ], 201);
        }
    }


    /**
     * Delete Job
     *
     * @response 200 {
     *   "message": "Job has been deleted successfully."
     * }
     *
     * @response 201 {
     *   "message": "Job not found."
     * }
     */
    public function delete($id)
    {
        try {
            // Find the job by ID and delete it
            $job = Job::findOrFail($id);
            $job->delete();

            // Return success response
            return response()->json([
                'message' => 'Job has been deleted successfully.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return not found error if job does not exist
            return response()->json([
                'message' => 'Job not found.'
            ], 201);
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            return response()->json([
                'message' => 'An error occurred while deleting the job.'
            ], 201);
        }
    }


    /**
     * Search Job
     *
     * @bodyParam query string required The title of the job. Example: abc
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "job_title": "Software Developer",
     *       "job_description": "Full stack developer needed...",
     *       "job_type": "Full-time",
     *       "job_location": "New York",
     *       "job_salary": 100000,
     *       "currency": "USD",
     *       "job_experience": 3,
     *       "contact_person": "John Doe",
     *       "contact_email": "johndoe@example.com"
     *     },
     *     ...
     *   ]
     * }
     * 
     * @response 201 {
     *   "message": "An error occurred during the search"
     * }
     */
    public function search(Request $request)
    {
        try {
            // Get parameters from request
            $sort_by = $request->get('sortby', 'id'); // Default to 'id' if not provided
            $sort_type = $request->get('sorttype', 'asc'); // Default to 'asc' if not provided
            $query = $request->get('query', '');
            $query = str_replace(" ", "%", $query); // Convert spaces to % for SQL LIKE query

            // // Validate sort parameters
            // if (!in_array($sort_by, ['id', 'job_title', 'job_location', 'job_salary']) || 
            //     !in_array($sort_type, ['asc', 'desc'])) {
            //     return response()->json([
            //         'message' => 'Invalid query parameters.'
            //     ], 400);
            // }

            // Perform search query
            $jobs = Job::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('job_title', 'like', '%' . $query . '%')
                        ->orWhere('job_description', 'like', '%' . $query . '%')
                        ->orWhere('job_type', 'like', '%' . $query . '%')
                        ->orWhere('job_location', 'like', '%' . $query . '%')
                        ->orWhere('job_salary', 'like', '%' . $query . '%')
                        ->orWhere('job_experience', 'like', '%' . $query . '%')
                        ->orWhere('contact_person', 'like', '%' . $query . '%')
                        ->orWhere('contact_email', 'like', '%' . $query . '%');
                })
                ->orderBy($sort_by, $sort_type)
                ->get(); // Get results

            return response()->json([
                'data' => $jobs
            ], 200); // Return the jobs as JSON

        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An error occurred during the search.'
            ], 201);
        }
    }



    ////
}
