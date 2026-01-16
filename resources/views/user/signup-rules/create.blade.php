@extends('user.layouts.master')
@section('title', isset($rule) ? 'Edit Signup Rule' : 'Create Signup Rule')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">{{ isset($rule) ? 'Edit' : 'Create' }} Signup Field Rule</h3>
                    <p class="text-muted small mb-0">Define validation rules for signup form fields</p>
                </div>
                <div>
                    <a href="{{ route('user.signup-rules.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-2"></i>Back to Rules
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form
                        action="{{ isset($rule) ? route('user.signup-rules.update', $rule->id) : route('user.signup-rules.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($rule))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Field Name -->
                            <div class="col-md-6 mb-3">
                                <label for="field_name" class="form-label">Field to Validate <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('field_name') is-invalid @enderror" id="field_name"
                                    name="field_name" required>
                                    <option value="">Select a field...</option>
                                    @foreach (\App\Models\SignupRule::getAvailableFields() as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('field_name', $rule->field_name ?? '') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('field_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Rule Type -->
                            <div class="col-md-6 mb-3">
                                <label for="rule_type" class="form-label">Rule Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('rule_type') is-invalid @enderror" id="rule_type"
                                    name="rule_type" required>
                                    <option value="">Select rule type...</option>
                                    @foreach (\App\Models\SignupRule::getRuleTypes() as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('rule_type', $rule->rule_type ?? '') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rule_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Rule Value -->
                            <div class="col-md-6 mb-3">
                                <label for="rule_value" class="form-label">Rule Value</label>
                                <input type="text" class="form-control @error('rule_value') is-invalid @enderror"
                                    id="rule_value" name="rule_value"
                                    value="{{ old('rule_value', $rule->rule_value ?? '') }}"
                                    placeholder="e.g., 10, gmail.com, /^[a-z]+$/">
                                <small class="text-muted" id="rule_value_help">Value depends on rule type selected</small>
                                @error('rule_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('priority') is-invalid @enderror"
                                    id="priority" name="priority" value="{{ old('priority', $rule->priority ?? 10) }}"
                                    min="0" required>
                                <small class="text-muted">Higher priority rules are checked first</small>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Error Message -->
                            <div class="col-12 mb-3">
                                <label for="error_message" class="form-label">Error Message</label>
                                <input type="text" class="form-control @error('error_message') is-invalid @enderror"
                                    id="error_message" name="error_message"
                                    value="{{ old('error_message', $rule->error_message ?? '') }}"
                                    placeholder="Custom error message shown to users">
                                @error('error_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="2" placeholder="Internal description of what this rule does">{{ old('description', $rule->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Is Critical -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_critical" name="is_critical"
                                        value="1"
                                        {{ old('is_critical', $rule->is_critical ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_critical">
                                        <strong class="text-danger">Critical Rule</strong>
                                        <small class="text-muted d-block">If enabled, failing this rule makes user
                                            INACTIVE</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $rule->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active</strong>
                                        <small class="text-muted d-block">Enable this rule immediately</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('user.signup-rules.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-2"></i>{{ isset($rule) ? 'Update' : 'Create' }} Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fa fa-question-circle text-info me-2"></i>Rule Type Examples</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rule Type</th>
                                    <th>Description</th>
                                    <th>Example Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>required</code></td>
                                    <td>Field must not be empty</td>
                                    <td><em>No value needed</em></td>
                                </tr>
                                <tr>
                                    <td><code>regex</code></td>
                                    <td>Match regular expression</td>
                                    <td><code>/^[a-zA-Z0-9_]+$/</code></td>
                                </tr>
                                <tr>
                                    <td><code>min_length</code></td>
                                    <td>Minimum character length</td>
                                    <td><code>2</code></td>
                                </tr>
                                <tr>
                                    <td><code>max_length</code></td>
                                    <td>Maximum character length</td>
                                    <td><code>50</code></td>
                                </tr>
                                <tr>
                                    <td><code>numeric</code></td>
                                    <td>Must be numeric only</td>
                                    <td><em>No value needed</em></td>
                                </tr>
                                <tr>
                                    <td><code>email_domain</code></td>
                                    <td>Email from specific domains</td>
                                    <td><code>gmail.com,yahoo.com</code></td>
                                </tr>
                                <tr>
                                    <td><code>phone_length</code></td>
                                    <td>Exact phone number length</td>
                                    <td><code>10</code></td>
                                </tr>
                                <tr>
                                    <td><code>contains</code></td>
                                    <td>Must contain specific text</td>
                                    <td><code>@company.com</code></td>
                                </tr>
                                <tr>
                                    <td><code>not_contains</code></td>
                                    <td>Must NOT contain text</td>
                                    <td><code>test</code></td>
                                </tr>
                                <tr>
                                    <td><code>in_list</code></td>
                                    <td>Must be in comma-separated list</td>
                                    <td><code>USA,Canada,Mexico</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <strong><i class="fa fa-exclamation-triangle me-2"></i>Critical Rules:</strong>
                        When a user fails a critical rule during signup, they are registered but their status is set to
                        <strong>INACTIVE</strong>.
                        Non-critical rules are warnings only and don't affect user status.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .gap-2 {
            gap: 0.5rem;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
        }

        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.9em;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Update help text based on selected rule type
        document.getElementById('rule_type').addEventListener('change', function() {
            const ruleType = this.value;
            const helpText = document.getElementById('rule_value_help');
            const ruleValueInput = document.getElementById('rule_value');

            const examples = {
                'required': 'No value needed',
                'regex': 'e.g., /^[a-zA-Z0-9_]+$/',
                'min_length': 'e.g., 2',
                'max_length': 'e.g., 50',
                'numeric': 'No value needed',
                'email_domain': 'e.g., gmail.com,yahoo.com,outlook.com',
                'phone_length': 'e.g., 10',
                'contains': 'e.g., @company.com',
                'not_contains': 'e.g., test',
                'min_value': 'e.g., 18',
                'max_value': 'e.g., 100',
                'in_list': 'e.g., USA,Canada,Mexico'
            };

            if (examples[ruleType]) {
                helpText.textContent = examples[ruleType];

                // Clear value for rules that don't need it
                if (ruleType === 'required' || ruleType === 'numeric') {
                    ruleValueInput.value = '';
                    ruleValueInput.placeholder = 'Not required for this rule type';
                } else {
                    ruleValueInput.placeholder = examples[ruleType];
                }
            }
        });

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
