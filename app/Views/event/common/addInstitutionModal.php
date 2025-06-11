
<!-- Add Institution Modal -->
<div class="modal fade shadow" id="addInstitutionModal" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background-color:gainsboro">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Institution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5 pt-0">
<!--                <div class="pb-3"><small style="color:red">Tip: you can directly search the city, just type the city and select its right location from dropdown.</small></div>-->
                <div>
                    <div class="input-group ">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionName">Institution Name</label>
                        <input type="text"  name="institutionName"  class="form-control shadow-none" id="institutionName" style="max-width:800px" placeholder="">
                    </div>
                    <small style="color:red">Please enter the institution name ONLY. Do not use all capital letters.</small>
                    <div class="input-group ">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionCity">City</label>
                        <input type="text"  name="institutionCity"  class="form-control shadow-none rounded" id="institutionCity" style="max-width:500px" placeholder="">
                        <input type="text"  name="institutionCityId"  class="form-control shadow-none rounded" id="institutionCityId" style="max-width:500px; display:none; display:none;" placeholder="">
                    </div>
                    <small style="color:red">Please type in the first four letters of your city.  Once the cities appear, select from the list.</small>
                    <div class="input-group" style="display: none">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionProvince">State/Province</label>
                        <input type="text"  name="institutionState"  class="form-control shadow-none rounded" id="institutionState" style="max-width:500px" placeholder="" readonly>
                        <input type="text"  name="institutionStateId"  class="form-control shadow-none rounded" id="institutionStateId" style="max-width:500px; display:none;" placeholder="" readonly>
                    </div>

                    <div class="input-group ">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionCountry">Country</label>
                        <input type="text"  name="institutionCountry"  class="form-control shadow-none rounded" id="institutionCountry" style="max-width:400px" placeholder="" readonly>
                        <input type="text"  name="institutionCountryId"  class="form-control shadow-none rounded" id="institutionCountryId" style="max-width:400px; display:none" placeholder="" readonly>
                    </div>


                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary submitNewInstitutionBtn">Submit Institution</button>
            </div>
        </div>
    </div>
</div>
