<script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
<div  class="container" >
    <div class="row justify-content-center">
        <div class="container">
            <div class="card">
                <div class="card-header">


                        <h2> Path planning </h2>
                            <form name="FormData" id ="myForm" method="post" >
                                <div class="wrapper">
                                    <div class="form-group row">
                                        <div class="col-sm-12 col-md-8 col-lg-9">
                                        </div>
                                    </div>
                                </div>
                                <p><button type="button" class="btn btn-success" id="add_fields"><i class="fa fa-plus"></i> Add Path</button>
                                <button type="submit" class="btn btn-primary" id="automate"><i class="fa fa-plus"></i> Automate</button>

                            </p>
                            </form>


                </div>
            </div>
        </div>
    </div>
</div>





<script>
console.log('isclicked');

var x = 0; // Initial input field is set to 0

document.addEventListener('DOMContentLoaded', function() {
    var max_fields = 10; // Maximum allowed input fields
    var wrapper = document.querySelector(".wrapper"); // Input fields wrapper
    var add_button = document.getElementById("add_fields"); // Add button class or ID

    // When user clicks on add input button
    add_button.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('isclicked');
        // Check maximum allowed input fields
        if (x < max_fields) {
            x++; // Input field increment
            // Generate unique names and ids
            var uniqueName = 'input_array_name[' + x + ']';
            var uniqueId = 'input_array_id_' + x;
            // Add input field
            var inputField = document.createElement('div');
            inputField.className = 'form-group row';
            inputField.innerHTML = '<div class="col-sm-12 col-md-8 col-lg-9"><input type="text" name="' + uniqueName + '" id="' + uniqueId + '" placeholder="Input Movement Here" class="form-control" value="" /> </div><a href="javascript:void(0);" class="remove_field">Remove</a>';
            wrapper.appendChild(inputField);
        }
    });

    // When user clicks on remove button
    wrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove_field')) {
            e.preventDefault();
            e.target.parentElement.remove(); // Remove input field
            x--; // Input field decrement
        }
    });

    document.getElementById('myForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        var dynamicInputValues = [];

        for (var i = 1; i <= x; i++) {
            var inputName = 'input_array_name[' + i + ']';
            var inputElement = document.querySelector('[name="' + inputName + '"]');
            var inputValue = inputElement ? inputElement.value : '';
            dynamicInputValues.push(inputValue);
        }

        document.getElementById("myForm").reset();

        console.log(dynamicInputValues);
        startTime = new Date();
        droneStartTime = formatDateTime(startTime);
        console.log(droneStartTime);

        // Send data to the server using Fetch API
        try {
            var response = await fetch('http://127.0.0.1:5000/automated', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dynamicInputValues)
            });

            var data = await response.json();
            // Handle the response from the server
            console.log(data);
        } catch (error) {
            // Handle errors if any
            console.error(error);
        }

        endTime = new Date();
        droneEndTime = formatDateTime(endTime);
    });
});



</script>
