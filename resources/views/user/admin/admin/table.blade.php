@foreach ($admins as $admin)
    <tr>
        <td>{{ $admin->user_name }}</td>
        <td>{{ $admin->full_name }}</td>
        <td>{{ $admin->email }}</td>
        <td>{{ $admin->phone }}</td>
        <td>{{ date('d M Y', strtotime($admin->created_at)) }}</td>
        <td align="center">
            <div class="edit-1 d-flex align-items-center justify-content-center">
                @if (auth()->user()->can('Edit Admin List'))
                    <a class="edit-admins edit-icon" href="#" data-bs-toggle="modal" data-bs-target="#edit_admin"
                        data-id="{{ $admin->id }}" data-route="{{ route('user.admin.edit', $admin->id) }}"> <span
                            class="edit-icon"><i class="fas fa-edit"></i></span></a>
                @endif
                @if (auth()->user()->can('Delete Admin List'))
                    <a href="{{ route('user.admin.delete', $admin->id) }}"
                        onclick="return confirm('Are you sure to delete this admin?')"> <span class="trash-icon"><i
                                class="fas fa-trash"></i></span></a>
                @endif
            </div>

        </td>
    </tr>
@endforeach
