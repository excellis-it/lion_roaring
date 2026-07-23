@php
    $inviteeSelectConfig = [
        'eligibleUsersUrl' => route('private-collaborations.get-eligible-users'),
        'invitedUsers' => $invitedUsers ?? [],
        'collaborationCountryId' => $collaborationCountryId ?? null,
        'preloadFromDom' => $preloadFromDom ?? false,
        'enableCountryReload' => $enableCountryReload ?? false,
    ];
@endphp
(function($) {
        var inviteeConfig = @json($inviteeSelectConfig);
        var invitedUsers = inviteeConfig.invitedUsers || [];
        var invitedUserIds = invitedUsers.map(function(u) {
            return u.id;
        });

        function appendInviteeOption($select, user, selected) {
            var option = new Option(user.text, user.id, false, !!selected);
            $select.append(option);
        }

        function destroyInviteesSelectIfNeeded() {
            var $invitees = $('#invitees');
            if ($invitees.hasClass('select2-hidden-accessible')) {
                $invitees.select2('destroy');
            }
        }

        function clearInviteesSearchField() {
            var select2 = $('#invitees').data('select2');
            if (!select2) {
                return;
            }

            if (select2.$container) {
                select2.$container.find('.select2-search__field').val('');
            }

            if (select2.dropdown && select2.dropdown.$search) {
                select2.dropdown.$search.val('');
            }
        }

        function renderInviteeSelection(data) {
            if (!data.id) {
                return data.text;
            }

            var m = String(data.text || '').match(/^(.*) <(.*)>$/);
            return m ? m[1] : data.text;
        }

        function initInviteesSelect() {
            var $invitees = $('#invitees');
            if (!$invitees.length || typeof $.fn.select2 === 'undefined') {
                return;
            }

            destroyInviteesSelectIfNeeded();

            $invitees.select2({
                placeholder: 'Select users to invite',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                templateSelection: renderInviteeSelection
            });

            $invitees.off('select2:select.inviteesSearch').on('select2:select.inviteesSearch', function() {
                clearInviteesSearchField();
            });
        }

        function populateInviteeOptions(users) {
            var $invitees = $('#invitees');
            $invitees.empty().append('<option></option>');

            var appendedIds = [];
            $.each(users || [], function(index, user) {
                appendInviteeOption(
                    $invitees,
                    user,
                    invitedUserIds.includes(user.id)
                );
                appendedIds.push(user.id);
            });

            $.each(invitedUsers, function(index, user) {
                if (appendedIds.indexOf(user.id) === -1) {
                    appendInviteeOption($invitees, user, true);
                }
            });
        }

        function loadUsersForCountry(countryId, onLoaded) {
            if (!countryId) {
                if (typeof onLoaded === 'function') {
                    onLoaded();
                }
                return;
            }

            $.ajax({
                url: inviteeConfig.eligibleUsersUrl,
                type: 'GET',
                data: {
                    country_id: countryId
                },
                success: function(response) {
                    if (response.status && response.users) {
                        populateInviteeOptions(response.users);
                    }
                    if (typeof onLoaded === 'function') {
                        onLoaded();
                    }
                },
                error: function(xhr) {
                    console.error('Failed to load users:', xhr);
                    toastr.error('Failed to load users for selected country.');
                }
            });
        }

        function getCountryIdForUserLoading() {
            if ($('#countries').length) {
                return $('#countries').val() || inviteeConfig.collaborationCountryId;
            }

            return inviteeConfig.collaborationCountryId;
        }

        function bootstrapInviteesSelect() {
            if (inviteeConfig.preloadFromDom) {
                initInviteesSelect();
                return;
            }

            var countryId = getCountryIdForUserLoading();
            if (!countryId) {
                initInviteesSelect();
                return;
            }

            loadUsersForCountry(countryId, initInviteesSelect);
        }

        function setupCountryUserLoading() {
            $('#countries').off('change.inviteesCountry').on('change.inviteesCountry', function() {
                var countryId = $(this).val();
                destroyInviteesSelectIfNeeded();
                $('#invitees').empty().append('<option></option>');

                if (!countryId) {
                    initInviteesSelect();
                    return;
                }

                loadUsersForCountry(countryId, initInviteesSelect);
            });
        }

        function startInviteesSelect() {
            if (!$('#invitees').length) {
                return;
            }

            if (typeof $.fn.select2 === 'undefined') {
                $.getScript('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js')
                    .done(function() {
                        bootstrapInviteesSelect();
                        if (inviteeConfig.enableCountryReload) {
                            setupCountryUserLoading();
                        }
                    });
                return;
            }

            bootstrapInviteesSelect();
            if (inviteeConfig.enableCountryReload) {
                setupCountryUserLoading();
            }
        }

        window.PrivateCollaborationInvitees = {
            start: startInviteesSelect,
            init: initInviteesSelect,
            destroy: destroyInviteesSelectIfNeeded,
            loadUsersForCountry: loadUsersForCountry
        };

    startInviteesSelect();
})(jQuery);
