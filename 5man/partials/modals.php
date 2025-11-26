<!-- ================= LAB MODALS ================= -->
<div class="modal fade" id="addLabModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addLabForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Lab</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Lab Name</label>
            <input type="text" name="lab_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Color</label>
            <input type="color" name="color" class="form-control" value="#2196f3">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="manageLabsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Labs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <?php foreach ($labs as $lab): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($lab['lab_name']) ?>
              <button class="btn btn-sm btn-danger delete-lab" data-id="<?= $lab['id'] ?>">Delete</button>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- ================= SECTION MODALS ================= -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addSectionForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Section</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Section Name</label>
            <input type="text" name="section_name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="manageSectionsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Sections</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <?php foreach ($sections as $sec): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($sec['section_name']) ?>
              <button class="btn btn-sm btn-danger delete-section" data-id="<?= $sec['id'] ?>">Delete</button>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- ================= PROFESSOR + SUBJECT MODALS ================= -->
<div class="modal fade" id="addProfessorSubjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addProfessorSubjectForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Professor & Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Professor Name</label>
            <input type="text" name="professor_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject</label>
            <select name="subject_id" class="form-select" required>
              <?php
              $subjects = $conn->query("SELECT id, subject_name FROM subjects ORDER BY subject_code")->fetch_all(MYSQLI_ASSOC);
              foreach ($subjects as $sub):
              ?>
                <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['subject_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="manageProfessorSubjectsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Professor & Subject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <?php foreach ($professorSubjects as $ps): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($ps['professor_name'] . " - " . $ps['subject_name']) ?>
              <button class="btn btn-sm btn-danger delete-professor-subject" data-id="<?= $ps['id'] ?>">Delete</button>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>