<div class="wrap">
    <h1>Advanced Block Rules</h1>
    <div class="notice notice-warning">
        <p><strong>Warning:</strong> Blocking based on advanced rules can restrict legitimate users from accessing your site. Ensure you know the implications of each rule.</p>
    </div>

    <!-- Add New Rule Form -->
    <form method="POST">
        <h3>Add New Rule</h3>
        <table class="form-table">
            <tr>
                <th><label for="rule_name">Rule Name</label></th>
                <td><input type="text" name="rule_name" id="rule_name" required></td>
            </tr>
            <tr>
                <th><label for="rule_type">Rule Type</label></th>
                <td>
                    <select name="rule_type" id="rule_type">
                        <option value="ip">IP</option>
                        <option value="user_agent">User Agent</option>
                        <option value="country">Country</option>
                        <option value="request_uri">Request URI</option>
                        <option value="full_url">Full URL</option>
                        <option value="known_bot">Known Bot</option>
                        <option value="bot_category">Bot Category</option>
                        <option value="query_strings">Query Strings</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="condition_value">Condition</label></th>
                <td>
                    <div id="condition_field">
                        <input type="text" id="condition_value" name="condition_value" placeholder="Enter condition" required >
                    </div> 
                    
                </td>
            </tr>
            <tr>
                <th><label for="rule_action">Action</label>
                </th>
                <td>
                 <div id="action_field" >
                    <select name="rule_action" id="rule_action">
                        <option value="block">Block</option>
                        <option value="redirect">Redirect</option>
                    </select>
                    
               </div>
                </td>
            </tr>
            <tr id="for_redirect" style="display:none">
            <th>
                <label for="redirect_url">Redirect Url</label>
            </th>  
            <td>
            <input type="URL" id="redirect_url" name="redirect_url" placeholder="Enter full url" >
            </td>
                   
            </tr>
        </table>
        <p><input type="submit" name="new_rule" class="button button-primary" value="Add Rule"></p>
    </form>

    <!-- Display Existing Rules -->
    <h3>Existing Rules</h3>
    <table class="widefat">
        <thead>
            <tr>
                <th><b>ID</b></th>
                <th><b>Rule Name</b></th>
                <th><b>Type</b></th>
                <th><b>Condition</b></th>
                <th><b>Action</b></th>
                <th><b>Delete</b></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rules as $rule): ?>
                <tr>
                    <td><?php echo esc_html($rule->id); ?></td>
                    <td><?php echo esc_html($rule->rule_name); ?></td>
                    <td><?php echo esc_html($rule->type); ?></td>
                    <td><?php 
                    if($rule->type == "country")
                    echo $country_class->get_country_by_code($rule->condition_value);
                    else
                    echo esc_html($rule->condition_value); ?></td>
                    <td><?php
                    if($rule->action == "redirect" ) 
                    echo esc_html("$rule->action >> $rule->redirect_url "); 
                    else 
                    echo esc_html($rule->action); ?></td>
                    <td><a href="?page=wp-bot-blocker-advanced-rules&delete_rule=<?php echo esc_attr($rule->id); ?>" class="button">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ruleTypeSelect = document.getElementById('rule_type');
        const conditionField = document.getElementById('condition_field');
        const countries = <?php echo json_encode($countries); ?>;
       
       
       const actionTypeSelect = document.getElementById('rule_action');
        
        const redirectField = document.getElementById('for_redirect');

        ruleTypeSelect.addEventListener('change', function () {
            if (this.value === 'country') {
                // Replace condition input with a select dropdown for countries
                let select = document.createElement('select');
                select.name = 'condition_value';
                select.id = 'condition_value';

                // Populate country options
                countries.forEach(country => {
                    let option = document.createElement('option');
                    option.value = country.code;
                    option.textContent = country.name;
                    select.appendChild(option);
                });

                conditionField.innerHTML = ''; // Clear existing field
                conditionField.appendChild(select);
            }
           else if (this.value === 'known_bot') {
                // Replace condition with disabled input 
                let input = document.createElement('input');
                input.type = 'text';
                input.name = 'condition_value';
                input.id = 'condition_value';
                input.value='any_know_bot';
                input.placeholder = 'Any Identified Bot';
                input.readOnly = true;

                conditionField.innerHTML = ''; // Clear existing field
                conditionField.appendChild(input);
            }
            else if (this.value === 'bot_category') {
                
                const bots = <?php echo json_encode($bot_cat); ?>;

                // Replace condition input with a select dropdown for bots
                let select = document.createElement('select');
                select.name = 'condition_value';
                select.id = 'condition_value';

                // Populate bots options
                bots.forEach(bot=> {
                    let option = document.createElement('option');
                    option.value = bot.type;
                    option.textContent = bot.name;
                    select.appendChild(option);
                });

                conditionField.innerHTML = ''; // Clear existing field
                conditionField.appendChild(select);
            }
            else if (this.value === 'full_url') 
            {
                create_input("url", "Enter full link. * not allowed"); 
            
            } 
            else {
                // Revert to text input for other rule types
                
                create_input("text", "Enter condition value");
            }
        }
        );
        
        actionTypeSelect.addEventListener('change', function () {
            if (this.value === 'redirect') 
            {
              redirectField.style.display = "block" ;
            }
            else
            {
                redirectField.style.display = "none" ;
            }
            
        }) ;
    });
    
 function create_input(type, placeholder)
{ 
    let input = document.createElement('input');
                input.type = type;
                input.name = 'condition_value';
                input.id = 'condition_value';
                input.placeholder = placeholder ;

                conditionField = document.getElementById('condition_field');
                conditionField.innerHTML = ''; // Clear existing field
                conditionField.appendChild(input);
                
            
}


</script>

