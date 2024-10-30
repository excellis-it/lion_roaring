<div class="box_slae" id="create_mail_box1">
    <div id="deletebtn" onclick="dltFun();"><i class="fas fa-times"></i></div>
    <div class='popup-window new-mail'>
        <div class='header'>
            <div class='title'>New Message
            </div>
        </div>
        <form action="{{ route('mail.send') }}" method="POST" id="sendUserEMailForm" enctype="multipart/form-data">
            @csrf
            <div class='min-hide'>
                <input id="compose_to" name="to" class='receiver input-large' type='text' placeholder='Recipients'
                    value='' />
                <input id="compose_cc" name="cc" class='receiver input-large' type='text' placeholder='CC' value='' />
                <input class='input-large' name="subject" type='text' placeholder='Subject' />
            </div>
            <textarea class='min-hide_textera' name="message" rows="6" placeholder='Message'></textarea>

            <div class="m-2" id="create-mail-selected-file-names"></div>

            <div class='menu min-hide'>
                <button type="submit" class='button-large button-blue'>Send</button>
                <div class="file-input">
                    <input type="file" name="attachments[]" id="create-mail-file-input" class="file-input__input"
                        multiple />
                    <label class="file-input__label" for="create-mail-file-input">
                        <span><i class='fa fa-paperclip'></i></span>
                    </label>
                </div>
                <div class='trash_btn'>
                    <button onclick="clearMailForm()" type="button" class='button-large button-silver'><i
                            class='fa fa-trash'></i></button>
                </div>
            </div>
        </form>



    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            // Ensure that you are encoding this correctly
            const userEmails = {!! json_encode($allMailIds->pluck('email')) !!};

            // Initialize Tagify for "To" and "CC" fields
            const toInput = document.getElementById('compose_to');
            const ccInput = document.getElementById('compose_cc');

            const tagifyTo = new Tagify(toInput, {
                whitelist: userEmails,
                enforceWhitelist: true,
                dropdown: {
                    maxItems: 20,
                    maxTags: 1, 
                    classname: "tags-dropdown",
                    enabled: 0, // all ways to be enabled
                    closeOnSelect: true, // keep dropdown open after selection
                    highlight: true // highlight matched results
                }
            });

            const tagifyCC = new Tagify(ccInput, {
                whitelist: userEmails,
                enforceWhitelist: true,
                dropdown: {
                    maxItems: 20, // Adjust the max items shown in the dropdown
                    classname: "tags-dropdown",
                    enabled: 0, // all ways to be enabled
                    closeOnSelect: false, // keep dropdown open after selection
                    highlight: true // highlight matched results
                }
            });
        });
</script>