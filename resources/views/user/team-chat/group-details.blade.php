@if (isset($group_details))
<div class="modal fade" id="exampleModalToggle3" aria-hidden="true" aria-labelledby="exampleModalToggleLabel3"
    tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content group_create">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel3">Change Group Name And Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('team-chats.name-des-update') }}" method="post" enctype="multipart/form-data"
                id="name-des-update">
                @csrf
                <input type="hidden" name="team_id" value="{{$team['id']}}">
                <div class="modal-body">
                    <div class="group_crate">
                        <div class="mb-3">
                            <label for="" class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="" placeholder="" name="name" value="{{$team['name']}}">
                        </div>
                        <div class="">
                            <label for="" class="form-label">Description</label>
                            <textarea class="form-control" id="" rows="3" name="description">{{$team['description']}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <button type="button" class="btn btn-dark back-to-group-info" data-team-id="{{$team['id']}}">Back </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endif
