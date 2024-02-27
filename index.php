<!DOCTYPE html>
<html>
<head>
    <title>Event Planner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f3f3f3;
        }

        .form_container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .form_wrapper {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 0 0 calc(30% - 20px);
        }

        h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }

        .input_fields_wrap {
            margin-bottom: 20px;
        }

        label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }

        input[type="text"] {
            padding: 8px;
            margin-bottom: 10px;
            width: calc(100% - 120px);
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .add_field_button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .add_field_button:hover {
            background-color: #45a049;
        }

        .total {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }

        #save_button {
            background-color: #008CBA;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        #save_button:hover {
            background-color: #005f7f;
        }

        #section_totals {
            margin-top: 20px;
        }

        #overall_total {
            font-weight: bold;
            font-size: 20px;
            margin-top: 20px;
        }

        @media screen and (max-width: 768px) {
            .form_wrapper {
                flex: 0 0 calc(100% - 20px);
            }
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            function calculateSectionTotal(wrapper) {
                var sectionTotal = 0;
                $(wrapper).find("input[name='items[price][]']").each(function() {
                    var price = parseFloat($(this).val());
                    var quantity = parseFloat($(this).closest('div').find("input[name='items[quantity][]']").val());
                    sectionTotal += price * quantity;
                });
                return sectionTotal.toFixed(2);
            }

            function updateTotals() {
                var overallTotal = 0;
                $(".form_wrapper").each(function() {
                    var wrapper = $(this).find('.input_fields_wrap');
                    var sectionTotal = calculateSectionTotal(wrapper);
                    $(this).find('.total').text("Total: $" + sectionTotal);
                    overallTotal += parseFloat(sectionTotal);
                });
                $("#overall_total").text("Overall Total: $" + overallTotal.toFixed(2));
            }

            $(".add_field_button").click(function(e) {
                e.preventDefault();
                var wrapper = $(this).closest('.form_wrapper').find('.input_fields_wrap');
                $(wrapper).append('<div><label>Item Name:</label><input type="text" name="items[name][]"><label>Price:</label><input type="text" name="items[price][]"><label>Quantity:</label><input type="text" name="items[quantity][]"></div>');
                updateTotals();
            });

            updateTotals(); // Update totals when the page loads

            $("#save_button").click(function(e) {
                e.preventDefault();
                var data = {};
                $(".form_wrapper").each(function(index) {
                    var wrapper = $(this).find('.input_fields_wrap');
                    var sectionName = $(this).find('h2').text().trim().toLowerCase(); // Get section name
                    data[sectionName] = {
                        items: $(wrapper).find("input[name^='items[name]']").map(function() { return $(this).val(); }).get(),
                        prices: $(wrapper).find("input[name^='items[price]']").map(function() { return parseFloat($(this).val()); }).get(),
                        quantities: $(wrapper).find("input[name^='items[quantity]']").map(function() { return parseInt($(this).val()); }).get(),
                        total: calculateSectionTotal(wrapper)
                    };
                });

                $.ajax({
                    type: 'POST',
                    url: '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>',
                    data: {items: data},
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        alert("Data saved successfully.");
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert("Error occurred while saving data.");
                    }
                });
            });
        });
    </script>
</head>
<body>

<div class="form_container">
    <div class="form_wrapper">
        <h2>Drinks</h2>
        <form method="post">
            <div class="input_fields_wrap">
                <button class="add_field_button">Add</button><br><br>
            </div>
            <span class="total"></span>
        </form>
    </div>

    <div class="form_wrapper">
        <h2>Food</h2>
        <form method="post">
            <div class="input_fields_wrap">
                <button class="add_field_button">Add</button><br><br>
            </div>
            <span class="total"></span>
        </form>
    </div>

    <div class="form_wrapper">
        <h2>Decoration</h2>
        <form method="post">
            <div class="input_fields_wrap">
                <button class="add_field_button">Add</button><br><br>
            </div>
            <span class="total"></span>
        </form>
    </div>
</div>

<div id="section_totals">
    <span id="overall_total">Overall Total: $0.00</span>
</div>

<button id="save_button">Save</button>

</body>
</html>
