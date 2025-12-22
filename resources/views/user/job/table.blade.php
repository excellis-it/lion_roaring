@if (count($jobs) > 0)
    @foreach ($jobs as $key => $job)
        <tr>
            <td>{{ $jobs->firstItem() + $key }}</td>
            <td>{{ $job->job_title ? $job->job_title : '-' }}</td>
            <td>{{ $job->job_type ? $job->job_type : '-' }}</td>
            <td>{{ $job->job_location ? $job->job_location : '-' }}</td>
            <td> {{ $job->job_salary ? $job->currency : '' }} {{ $job->job_salary ? $job->job_salary . '/' : 0 . '/' }}
                {{ $job->list_of_values ? $job->list_of_values : '' }}</td>
            <td>{{ $job->job_experience ? $job->job_experience : 0 }} {{ $job->job_experience > 1 ? 'years' : 'year' }}
            </td>
            {{-- contact_person --}}
            <td>
                {{ $job->contact_person ? $job->contact_person : '-' }}
            </td>
            <td>
                {{ $job->contact_email ? $job->contact_email : '-' }}
            </td>
            <td>
                {{ date('d M, Y', strtotime($job->created_at)) }}
            </td>
            <td>
                {{ $job->country->name ?? '-' }}
            </td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit Job Postings'))
                        <a href="{{ route('jobs.edit', $job->id) }}" class="delete_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('View Job Postings'))
                        <a href="{{ route('jobs.show', $job->id) }}" class="delete_icon">
                            <i class="fa-solid fa-eye"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Delete Job Postings'))
                        <a href="javascript:void(0)" id="delete" data-route="{{ route('jobs.delete', $job->id) }}"
                            class="delete_icon">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="11">
            <div class="d-flex justify-content-center">
                {!! $jobs->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="11" class="text-center">No data found</td>
    </tr>
@endif
