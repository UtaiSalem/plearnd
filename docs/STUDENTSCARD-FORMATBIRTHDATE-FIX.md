# StudentsCard formatBirthDate Fix

## Problem
The Vue component `StudentsCard.vue` is trying to use a function called `formatBirthDate` on line 636, but this function is not defined in the component.

## Error Details
```
[Vue warn]: Property "formatBirthDate" was accessed during render but is not defined on instance.
TypeError: _ctx.formatBirthDate is not a function
```

## Root Cause Analysis
1. The component imports date utility functions from `@/utils/dateUtils`:
   - `formatDateForInput`
   - `formatDateThai` 
   - `calculateAge`

2. However, there's no `formatBirthDate` function in the dateUtils file

3. The template on line 636 uses: `{{ formatBirthDate(form.date_of_birth) }}`

## Solution
Replace `formatBirthDate` with `formatDateThai` which is already imported and available.

## Files to Modify
- `resources/js/Pages/Learn/Student/HomeVisit/Components/StudentsCard.vue`

## Specific Change Required
**Line 636:**
```vue
<!-- BEFORE -->
{{ formatBirthDate(form.date_of_birth) }}

<!-- AFTER -->
{{ formatDateThai(form.date_of_birth) }}
```

## Expected Result
- The Vue error will be resolved
- Birth dates will display in Thai format (e.g., "8 มกราคม 2553")
- The component will render without errors

## Testing Steps
1. Apply the fix
2. Load the page containing the StudentsCard component
3. Verify no Vue errors in console
4. Check that birth dates display correctly in Thai format
5. Test with different birth date values to ensure proper formatting

## Additional Notes
- The `formatDateThai` function is already imported (line 7)
- This function handles Buddhist Era conversion (year + 543)
- It provides proper Thai month names
- Error handling is built into the utility function