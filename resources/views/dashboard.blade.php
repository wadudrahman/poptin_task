@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 text-center align-items-center flex-row-reverse">
                <div class="col-lg-auto ms-lg-auto">
                    <a href="#" class="btn btn-outline-warning ms-auto @if($rules->isEmpty()) disabled @endif"
                       data-bs-toggle="modal" data-bs-target="#showScript">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="icon icon-tabler icons-tabler-outline icon-tabler-box-multiple">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"/>
                            <path d="M17 17v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h2"/>
                        </svg>
                        Show Script Code
                    </a>
                </div>
                <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                    <h2 class="page-title">
                        Configure Rules
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    @include('partials.alert')
                </div>
            </div>
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <form action="{{ route('storeRules') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="mb-3 col-sm-6 col-md-6">
                                        <label class="form-label required">Message</label>
                                        <input name="message" type="text" class="form-control" placeholder="Hello World"
                                               value="{{ auth()->user()->message }}" required>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                        <tr>
                                            <th><label class="form-label required">Action</label></th>
                                            <th><label class="form-label required">Condition</label></th>
                                            <th><label class="form-label required">URL Pattern</label></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($rules->isEmpty())
                                            <tr>
                                                <td>
                                                    <select class="form-select" name="action[]">
                                                        <option disabled selected>Select Action</option>
                                                        <option value="{{ \App\Helpers\EnumHelper::SHOW }}">Show On
                                                        </option>
                                                        <option value="{{ \App\Helpers\EnumHelper::HIDE }}">Don't Show
                                                            On
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="rule[]">
                                                        <option disabled selected>Select Rule</option>
                                                        <option value="{{ \App\Helpers\EnumHelper::CONTAINS }}">Pages
                                                            That Contains
                                                        </option>
                                                        <option value="{{ \App\Helpers\EnumHelper::EXACT }}">A Specific
                                                            Page
                                                        </option>
                                                        <option value="{{ \App\Helpers\EnumHelper::STARTS_WITH }}">Pages
                                                            Starting With
                                                        </option>
                                                        <option value="{{ \App\Helpers\EnumHelper::ENDS_WITH }}">Pages
                                                            Ending With
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text"><b>www.domain.com/</b></span>
                                                        <input type="text" class="form-control" name="url[]"
                                                               autocomplete="off">
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-icon btn-google w-100 text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                             stroke-width="2" stroke-linecap="round"
                                                             stroke-linejoin="round"
                                                             class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M4 7l16 0"/>
                                                            <path d="M10 11l0 6"/>
                                                            <path d="M14 11l0 6"/>
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach($rules as $rule)
                                                <tr>
                                                    <td>
                                                        <select class="form-select" name="action[]">
                                                            <option disabled
                                                                    @if(!in_array($rule->action, [\App\Helpers\EnumHelper::SHOW, \App\Helpers\EnumHelper::HIDE])) selected @endif>
                                                                Select Action
                                                            </option>
                                                            <option value="{{ \App\Helpers\EnumHelper::SHOW }}"
                                                                    @if($rule->action === \App\Helpers\EnumHelper::SHOW) selected @endif>
                                                                Show On
                                                            </option>
                                                            <option value="{{ \App\Helpers\EnumHelper::HIDE }}"
                                                                    @if($rule->action === \App\Helpers\EnumHelper::HIDE) selected @endif>
                                                                Don't Show On
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-select" name="rule[]">
                                                            <option disabled
                                                                    @if(!in_array($rule->condition, [\App\Helpers\EnumHelper::CONTAINS, \App\Helpers\EnumHelper::EXACT, \App\Helpers\EnumHelper::STARTS_WITH, \App\Helpers\EnumHelper::EXACT])) selected @endif>
                                                                Select Rule
                                                            </option>
                                                            <option value="{{ \App\Helpers\EnumHelper::CONTAINS }}"
                                                                    @if($rule->condition === \App\Helpers\EnumHelper::CONTAINS) selected @endif>
                                                                Pages That Contains
                                                            </option>
                                                            <option value="{{ \App\Helpers\EnumHelper::EXACT }}"
                                                                    @if($rule->condition === \App\Helpers\EnumHelper::EXACT) selected @endif>
                                                                A Specific Page
                                                            </option>
                                                            <option value="{{ \App\Helpers\EnumHelper::STARTS_WITH }}"
                                                                    @if($rule->condition === \App\Helpers\EnumHelper::STARTS_WITH) selected @endif>
                                                                Pages Starting With
                                                            </option>
                                                            <option value="{{ \App\Helpers\EnumHelper::ENDS_WITH }}"
                                                                    @if($rule->condition === \App\Helpers\EnumHelper::ENDS_WITH) selected @endif>
                                                                Pages Ending With
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text"><b>www.domain.com/</b></span>
                                                            <input type="text" class="form-control" name="url[]"
                                                                   value="{{ $rule->url }}" autocomplete="off">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-icon btn-google w-100 text-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                 height="24"
                                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                 stroke-width="2" stroke-linecap="round"
                                                                 stroke-linejoin="round"
                                                                 class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M4 7l16 0"/>
                                                                <path d="M10 11l0 6"/>
                                                                <path d="M14 11l0 6"/>
                                                                <path
                                                                    d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                            </svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-secondary">Add Rule</button>
                                    <button type="submit" class="btn btn-primary ms-auto">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="showScript" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generated Script</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        &lt;script src="{{ config('app.url') }}/script/dynamic/{{ auth()->user()->uuid }}"&gt;&lt;/script&gt;
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to Add New Rule
        function addRuleRow() {
            // Get Table Body Element
            const tableBody = document.querySelector('table tbody');

            // Create New Row
            const newRow = document.createElement('tr');

            // Set Inner HTML for Row
            newRow.innerHTML = `
        <td>
            <select class="form-select" name="action[]">
                <option disabled selected>Select Action</option>
                <option value="show">Show On</option>
                <option value="hide">Don't Show On</option>
            </select>
        </td>
        <td>
            <select class="form-select" name="rule[]">
                <option disabled selected>Select Rule</option>
                <option value="contains">Pages That Contains</option>
                <option value="exact">A Specific Page</option>
                <option value="starts_with">Pages Starting With</option>
                <option value="ends_with">Pages Ending With</option>
            </select>
        </td>
        <td>
            <div class="input-group mb-2">
                <span class="input-group-text"><b>www.domain.com/</b></span>
                <input type="text" class="form-control" name="url[]" autocomplete="off">
            </div>
        </td>
        <td>
            <a href="#" class="btn btn-icon btn-google w-100 text-center delete-row-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 7l16 0"/>
                    <path d="M10 11l0 6"/>
                    <path d="M14 11l0 6"/>
                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                </svg>
            </a>
        </td>
    `;

            // Append the Row
            tableBody.appendChild(newRow);

            // Event Listener to Delete
            newRow.querySelector('.delete-row-btn').addEventListener('click', function (e) {
                e.preventDefault();
                deleteRuleRow(this);
            });
        }

        // Delete Row
        function deleteRuleRow(element) {
            const row = element.closest('tr');
            row.remove();
        }

        // Event Listener to Add Rule
        document.querySelector('.btn-secondary').addEventListener('click', function (e) {
            e.preventDefault();
            addRuleRow();
        });

        // Attach Delete Listeners to Existing Rows on Page Load
        function attachDeleteListeners() {
            document.querySelectorAll('.delete-row-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    deleteRuleRow(this);
                });
            });
        }

        // Event Listener to Delete on Page Load
        document.querySelectorAll('.delete-row-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                deleteRuleRow(this);
            });
        });
    </script>
@endpush
