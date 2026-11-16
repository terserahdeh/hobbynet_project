{{--
  One repeatable row of the step form.
  $type: material | instruction | check     $i: array index (or __I__ for the JS template)     $row: existing values
--}}
@php $row = $row ?? []; @endphp

@if ($type === 'material')
    <div class="admin-row admin-row--material" data-row>
        <input type="hidden" name="materials[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
        <select name="materials[{{ $i }}][kind]" class="admin-input" aria-label="Tool or material">
            <option value="tool" @selected(($row['kind'] ?? 'tool') === 'tool')>Tool</option>
            <option value="material" @selected(($row['kind'] ?? 'tool') === 'material')>Material</option>
        </select>
        <input name="materials[{{ $i }}][name]" class="admin-input" value="{{ $row['name'] ?? '' }}" maxlength="255" placeholder="Crochet hook" aria-label="Name">
        <input name="materials[{{ $i }}][spec]" class="admin-input" value="{{ $row['spec'] ?? '' }}" maxlength="255" placeholder="4 mm (optional detail)" aria-label="Detail">
        <label class="admin-opt"><input type="checkbox" name="materials[{{ $i }}][is_optional]" value="1" @checked(! empty($row['is_optional']))> Optional</label>
        <button type="button" class="admin-icon-btn admin-icon-btn--danger" data-remove aria-label="Remove this row">&times;</button>
    </div>

@elseif ($type === 'instruction')
    <div class="admin-row admin-row--instruction" data-row>
        <input type="hidden" name="instructions[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
        <span class="admin-row__num" data-num>1</span>
        <textarea name="instructions[{{ $i }}][body]" class="admin-input" maxlength="2000" placeholder="Insert the hook into the next stitch, yarn over, and pull up a loop." aria-label="Instruction">{{ $row['body'] ?? '' }}</textarea>
        <span class="admin-row__tools">
            <button type="button" class="admin-icon-btn" data-up aria-label="Move up">&uarr;</button>
            <button type="button" class="admin-icon-btn" data-down aria-label="Move down">&darr;</button>
            <button type="button" class="admin-icon-btn admin-icon-btn--danger" data-remove aria-label="Remove this instruction">&times;</button>
        </span>
    </div>

@else
    <div class="admin-row admin-row--check" data-row>
        <input type="hidden" name="checklist[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
        <input name="checklist[{{ $i }}][label]" class="admin-input" value="{{ $row['label'] ?? '' }}" maxlength="255" placeholder="Complete 10 rows of single crochet" aria-label="Checklist item">
        <span class="admin-row__tools">
            <button type="button" class="admin-icon-btn" data-up aria-label="Move up">&uarr;</button>
            <button type="button" class="admin-icon-btn" data-down aria-label="Move down">&darr;</button>
            <button type="button" class="admin-icon-btn admin-icon-btn--danger" data-remove aria-label="Remove this item">&times;</button>
        </span>
    </div>
@endif
