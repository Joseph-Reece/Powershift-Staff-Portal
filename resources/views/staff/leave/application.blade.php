<x-app-layout>
    <x-slot name="title"></x-slot>
    <div>
        <x-panel>
            <x-slot name="title">{{ $action == 'create' ? 'New' : 'Edit' }} Leave Request</x-slot>
            <x-slot name="body">
                <form method="POST" action="{{ $action == 'create' ? route('storeLeave') : route('updateLeave') }}"
                    class="text-black w-full" data-turbo-frame="_top" enctype="multipart/form-data"
                    onsubmit="return confirm('Are you sure you want to submit this leave application?');">
                    @csrf
                    @if ($action == 'edit')
                        @method('PUT')
                        <input id="requisitionNo" type="hidden" name="requisitionNo"
                            value="{{ isset($requisition) ? $requisition->ApplicationCode : '' }}" />
                    @endif
                    <input id="isHourly" type="hidden" name="isHourly" value="" />
                    <x-grid>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Leave Type</x-slot>
                                <x-slot name="value">
                                    <x-select onChange="getLeaveBalance()" id="leaveType" name="leaveType">
                                        <option value="">--select--</option>
                                        @if ($leaveTypes != null && count($leaveTypes) > 0)
                                            @foreach ($leaveTypes as $type)
                                                <option value="{{ $type->Code }}"
                                                    {{ $action == 'edit' ? ($requisition->LeaveType == $type->Code ? 'selected' : '') : old('leaveType') }}>
                                                    {{ $type->Code }}</option>
                                            @endforeach
                                        @endif
                                    </x-select>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                        <x-grid-col>
                            <x-form-group>
                                <x-slot name="label">Available Days</x-slot>
                                <x-slot name="value">
                                    <x-loading />
                                    <x-badge id="leaveDays" class="hidden bg-green-600 font-semibold"></x-badge>
                                </x-slot>
                            </x-form-group>
                        </x-grid-col>
                    </x-grid>
                    <div class="hidden mt-2" id="leaveData">
                        <x-grid>
                            <x-grid-col class="hidden leaveHide" id="divStartDate">
                                <x-form-group>
                                    <x-slot name="label">Start Date</x-slot>
                                    <x-slot name="value">
                                        <x-input type="date" name="startDate" :required="false" id="startDate"
                                            onChange="getThisLeaveDates()"
                                            value="{{ $action == 'edit' && old('startDate') == null ? $requisition->StartDate : old('startDate') }}" />
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                            {{-- TODO: Implement Time? --}}
                            {{-- <x-grid-col class="hidden leaveHide" id="divStartDateTime">
                                <x-form-group>
                                    <x-slot name="label">Start Date Time</x-slot>
                                    <x-slot name="value">
                                        <x-input type="datetime-local" :required="false" name="startDateTime" id="startDateTime" onChange="getLeaveDates()" value="{{$action == 'edit' && old('startDateTime') == null? $requisition->Starting_Date:old('startDateTime')}}" />
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col> --}}
                            <x-grid-col class="hidden leaveHide" id="divEndDate">
                                <x-form-group>
                                    <x-slot name="label">End Date</x-slot>
                                    <x-slot name="value">
                                        <x-input type="date" name="endDate" :required="false" id="endDate"
                                            onChange="getThisLeaveDates()"
                                            value="{{ $action == 'edit' && old('endDate') == null ? $requisition->EndDate : old('endDate') }}" />
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                            <x-grid-col class="hidden leaveHide" id="divAppliedDays">
                                <x-form-group>
                                    <x-slot name="label">Applied Days</x-slot>
                                    <x-slot name="value">
                                        <x-loading class="onGetDates" />
                                        <span id="appliedDays">######</span>
                                        {{-- <x-select onChange="getLeaveDates()" :required="false" id="appliedDays" name="appliedDays">
                                            <option value="">--select--</option>
                                        </x-select> --}}
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                            {{-- <x-grid-col class="hidden leaveHide" id="divAppliedHours">
                                <x-form-group>
                                    <x-slot name="label">Applied Hours</x-slot>
                                    <x-slot name="value">
                                        <x-input type="number" name="appliedHours" :required="false" id="appliedHours" onChange="getLeaveDates()" value="{{$action == 'edit' && old('appliedHours') == null? $requisition->appliedHours:old('appliedHours')}}" />
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col> --}}
                            <x-grid-col>
                                <x-form-group>
                                    <x-slot name="label">Return Date</x-slot>
                                    <x-slot name="value">
                                        <x-loading class="onGetDates" />
                                        <span id="returnDate">######</span>
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                            <x-grid-col>
                                <x-form-group>
                                    <x-slot name="label">Reliever</x-slot>
                                    <x-slot name="value">
                                        <x-select name="reliever" id="reliever" class="tom-selects"
                                            placeholder="select">
                                            <option value=""></option>
                                            @if ($relievers != null)
                                                @foreach ($relievers as $reliever)
                                                    <option value="{{ $reliever->No }}"
                                                        {{ $action == 'edit' ? ($requisition->Reliever == $reliever->No ? 'selected' : '') : old('reliever') }}>
                                                        {{ $reliever->No . ' - ' . $reliever->FirstName . ' ' . $reliever->MiddleName . ' ' . $reliever->LastName }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </x-select>
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                            <x-grid-col class="sm:grid-cols-1">
                                <x-form-group>
                                    <x-slot name="label">Leave Reason</x-slot>
                                    <x-slot name="value">
                                        <x-textarea
                                            name="reason">{{ $action == 'edit' && old('reason') == null ? $requisition->Reasonforleave : old('reason') }}</x-textarea>
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                            <x-grid-col class="sm:grid-cols-1">
                                <x-form-group>
                                    <x-slot name="label">Request Leave Allowance</x-slot>
                                    <x-slot name="value">
                                        <x-select name="requestLeaveAllowance">
                                            <option value="0"
                                                {{ $action == 'edit' ? ($requisition->RequestLeaveAllowance == false ? 'selected' : '') : old('requestLeaveAllowance') }}>
                                                No</option>
                                            <option value="1"
                                                {{ $action == 'edit' ? ($requisition->RequestLeaveAllowance == true ? 'selected' : '') : old('requestLeaveAllowance') }}>
                                                Yes</option>
                                        </x-select>
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                            <x-grid-col>
                                <x-form-group>
                                    <x-slot name="label">Attachment</x-slot>
                                    <x-slot name="value">
                                        <input type="file" name="attachment" id="attachment" />
                                    </x-slot>
                                </x-form-group>
                            </x-grid-col>
                        </x-grid>
                        <div class="p-2 flex justify-center">
                            <x-jet-button class="rounded-full bg-blue-800" data-turbo="false">Submit</x-jet-button>
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-panel>
    </div>
    @push('scripts')
        {{-- <script src="C:\Portals\Staff Portal\node_modules\nice-select2\dist\js\nice-select2.js"></script> --}}
        <script>
            var action = "<?php echo $action; ?>";
            var isHourly = false;
            var elIsHourly = document.getElementById('isHourly');
            var appliedDays = null;
            if (action == 'edit') {
                appliedDays = "<?php echo isset($requisition) ? $requisition->DaysApplied : ''; ?>";
            }

            function fnOnLoad() {
                if (action == 'edit') {
                    getLeaveBalance();
                }
            }

            function getLeaveBalance() {
                var loader = document.getElementById('loader');
                var elLeaveType = document.getElementById('leaveType');
                var elLeaveDays = document.getElementById('leaveDays');
                var elAppliedDays = document.getElementById('appliedDays');
                var elDivAppliedDays = document.getElementById('divAppliedDays');
                var elLeaveData = document.getElementById('leaveData');
                var elAppliedHours = document.getElementById('appliedHours');
                var elDivAppliedHours = document.getElementById('divAppliedHours');
                var elDivStartDate = document.getElementById('divStartDate');
                var elDivEndDate = document.getElementById('divEndDate');
                var elStartDate = document.getElementById('startDate');
                var elEndDate = document.getElementById('endDate');
                var elStartDateTime = document.getElementById('startDateTime');
                var elDivStartDateTime = document.getElementById('divStartDateTime');
                var leaveHide = document.getElementsByClassName("leaveHide");
                const elReturnDate = document.getElementById('returnDate');
                elLeaveDays.classList.add('hidden');
                elLeaveData.classList.add('hidden');
                //elAppliedHours.value = '';
                for (let element of leaveHide) {
                    element.classList.add('hidden');
                }
                if (elLeaveType.value != '') {
                    loader.classList.remove('hidden');
                    axios.get('/staff/leave/balance/' + elLeaveType.value).then(response => {
                        console.log(response.data.data);
                        var leaveDays = response.data.data.balance;
                        pendingCount = response.data.data.pendingCount;
                        isHourly = response.data.data.isHourly;
                        elIsHourly.value = isHourly;
                        elLeaveDays.innerHTML = leaveDays;
                        if (leaveDays > 0) {
                            //clearSelect('appliedDays');
                            if (pendingCount == 0) {
                                if (isHourly == false) {
                                    for (var i = 1; i <= leaveDays; i++) {
                                        var elOption = document.createElement("option");
                                        elOption.textContent = i;
                                        elOption.value = i;
                                        elAppliedDays.appendChild(elOption);
                                    }
                                    elDivAppliedDays.classList.remove('hidden');
                                    elDivStartDate.classList.remove('hidden');
                                    elDivEndDate.classList.remove('hidden');
                                } else {
                                    elDivAppliedHours.classList.remove('hidden');
                                    elDivStartDateTime.classList.remove('hidden');
                                }
                                if (action == 'edit') {
                                    if (isHourly == false) {
                                        elAppliedDays.innerHTML = appliedDays;
                                    }
                                    // getLeaveDates();
                                }
                                elLeaveData.classList.remove('hidden');
                                new TomSelect("#reliever", {
                                    allowEmptyOption: false,
                                    create: false,
                                });
                            } else {
                                alert(
                                    'You cannot apply a new leave while there is another one of the same type that is pending approval.'
                                );
                            }
                        }
                        loader.classList.add('hidden');
                        elLeaveDays.classList.remove('hidden');
                    }).catch((error) => {
                        loader.classList.add('hidden');
                        elLeaveDays.classList.remove('hidden');
                    });
                }
            }

            function getLeaveDates() {
                var elLeaveType = document.getElementById('leaveType');
                var loader = document.getElementsByClassName("onGetDates");
                var elAppliedDays = document.getElementById('appliedDays');
                var elAppliedHours = document.getElementById('appliedHours');
                var elStartDate = document.getElementById('startDate');
                var elEndDate = document.getElementById('endDate');
                var elStartDateTime = document.getElementById('startDateTime');
                var elReturnDate = document.getElementById('returnDate');
                var appliedDuration = isHourly ? elAppliedHours.value : elAppliedDays.value;
                var starting = isHourly ? elStartDateTime.value : elStartDate.value;
                var ending = elEndDate.value;
                elEndDate.innerHTML = '';
                elReturnDate.innerHTML = '';
                if (isHourly && appliedDuration > 4) {
                    elAppliedHours.value = '';
                    alert('Oops! you cannot apply more than 4 hours on halfday leave. Kindly use the Annual leave type.');
                } else if (appliedDuration != '' && starting != '' && ending != '') {
                    for (let element of loader) {
                        element.classList.remove('hidden');
                    }
                    axios.get('/staff/leave/details', {
                        params: {
                            leaveType: elLeaveType.value,
                            startDate: starting, // Assumes starting is in YYYY-MM-DD format
                            endDate: ending // Assumes ending is in YYYY-MM-DD format
                        }
                    }).then(response => {
                        // Hide loaders
                        for (let element of loader) {
                            element.classList.remove('hidden');
                        }
                        // loader.forEach(element => element.classList.add('hidden'));

                        if (!response.data.success) {
                            alert(response.data.message || 'Failed to retrieve leave details. Please try again.');
                            elStartDate.value = '';
                            return;
                        }

                        const dates = response.data.data;
                        if (dates.isWeekend) {
                            alert('Leave start or end date cannot be on a weekend');
                            elStartDate.value = '';
                        } else {
                            elAppliedDays.innerHTML = dates.appliedDays;
                            elReturnDate.innerHTML = dates.returnDate;
                        }
                    }).catch(error => {
                        // Hide loaders
                        loader.forEach(element => element.classList.add('hidden'));

                        console.error('Error fetching leave details:', error);
                        alert('An error occurred while retrieving leave details. Please try again or contact support.');
                        elStartDate.value = '';
                    });
                }
            }

            function getThisLeaveDates() {
                const elLeaveType = document.getElementById('leaveType');
                const loader = document.getElementsByClassName('onGetDates');
                const elAppliedDays = document.getElementById('appliedDays');
                const elAppliedHours = document.getElementById('appliedHours');
                const elStartDate = document.getElementById('startDate');
                const elEndDate = document.getElementById('endDate');
                const elStartDateTime = document.getElementById('startDateTime');
                const elReturnDate = document.getElementById('returnDate');
                const errorContainer = document.getElementById('error-container');

                // Clear previous errors
                if (errorContainer) errorContainer.innerHTML = '';

                // Get input values
                const appliedDuration = isHourly ? elAppliedHours.value : elAppliedDays.value;
                const starting = isHourly ? elStartDateTime.value : elStartDate.value;
                const ending = elEndDate.value;

                // Clear previous outputs
                elAppliedDays.innerHTML = '';
                elReturnDate.innerHTML = '';

                // Validate inputs
                if (!elLeaveType.value || !starting || !ending || appliedDuration === '') {
                    showError('Please fill in all required fields (leave type, start date, end date, and duration).');
                    return;
                }

                // Validate hourly leave duration
                if (isHourly && parseFloat(appliedDuration) > 4) {
                    elAppliedHours.value = '';
                    showError('You cannot apply for more than 4 hours on a half-day leave. Please use Annual leave type.');
                    return;
                }

                // Validate date formats
                const isValidDate = date => {
                    try {
                        return !isNaN(new Date(date).getTime());
                    } catch (e) {
                        return false;
                    }
                };

                if (!isValidDate(starting) || !isValidDate(ending)) {
                    showError('Invalid date format. Please use YYYY-MM-DD for dates or a valid datetime for hourly leaves.');
                    elStartDate.value = isHourly ? elStartDate.value : '';
                    elStartDateTime.value = isHourly ? '' : elStartDateTime.value;
                    return;
                }

                // Show loaders
                Array.from(loader).forEach(element => element.classList.remove('hidden'));

                // Make Axios request
                axios.get('/staff/leave/details', {
                    params: {
                        leaveType: elLeaveType.value,
                        startDate: isHourly ? new Date(starting).toISOString().split('T')[0] : starting,
                        endDate: ending
                    }
                }).then(response => {
                    // Hide loaders
                    Array.from(loader).forEach(element => element.classList.add('hidden'));

                    if (!response.data.success) {
                        showError(response.data.message || 'Failed to retrieve leave details. Please try again.');
                        elStartDate.value = isHourly ? elStartDate.value : '';
                        elStartDateTime.value = isHourly ? '' : elStartDateTime.value;
                        return;
                    }

                    const dates = response.data.data;
                    console.log(dates);
                    if (dates.isWeekend) {
                        showError('Leave start or end date cannot be on a weekend.');
                        elStartDate.value = isHourly ? elStartDate.value : '';
                        elStartDateTime.value = isHourly ? '' : elStartDateTime.value;
                    } else {
                        // elAppliedDays.innerHTML = dates.appliedDays;
                        elReturnDate.innerHTML = dates.returnDate;
                    }
                }).catch(error => {
                    // Hide loaders
                    Array.from(loader).forEach(element => element.classList.add('hidden'));

                    console.error('Error fetching leave details:', error);
                    showError('An error occurred while retrieving leave details. Please try again or contact support.');
                    elStartDate.value = isHourly ? elStartDate.value : '';
                    elStartDateTime.value = isHourly ? '' : elStartDateTime.value;
                });

                // Helper function to show errors
                function showError(message) {
                    if (errorContainer) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-danger';
                        errorDiv.textContent = message;
                        errorContainer.appendChild(errorDiv);
                        setTimeout(() => errorDiv.remove(), 5000);
                    } else {
                        alert(message);
                    }
                }
            }

            function clearSelect(elId) {
                el = document.getElementById(elId);
                while (el.options.length > 0) {
                    el.remove(0);
                }
                var elOption = document.createElement("option");
                elOption.textContent = '--select--';
                elOption.value = '';
                el.appendChild(elOption);
            }
        </script>
    @endpush
</x-app-layout>
