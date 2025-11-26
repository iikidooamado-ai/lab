$(document).ready(function () {
  // ==========================
  // ADD LAB
  // ==========================
  $("#addLabForm").submit(function (e) {
    e.preventDefault();
    $.post("../ajax/add_lab.php", $(this).serialize(), function (res) {
      if (res.success) {
        showToast("✅ Lab added successfully", "success");
        $("#addLabModal").modal("hide");
        refreshLabs();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to add lab", "error");
      }
    }, "json");
  });

  // DELETE LAB
  $(document).on("click", ".delete-lab", function () {
    const id = $(this).data("id");
    $.get("../ajax/delete_lab.php", { id }, function (res) {
      if (res.success) {
        showToast("🗑️ Lab deleted successfully", "success");
        refreshLabs();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to delete lab", "error");
      }
    }, "json");
  });

  function refreshLabs() {
    $.get("../ajax/get_labs.php", function (res) {
      if (res.success) {
        // Refresh dropdown
        const select = $("#lab_id");
        select.empty().append('<option value="">-- Select Lab --</option>');
        res.data.forEach(lab => {
          select.append(`<option value="${lab.id}">${lab.lab_name}</option>`);
        });

        // Refresh manage modal list
        const list = $("#labsList");
        list.empty();
        res.data.forEach(lab => {
          list.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
              ${lab.lab_name}
              <button class="btn btn-sm btn-danger delete-lab" data-id="${lab.id}">Delete</button>
            </li>
          `);
        });
      }
    }, "json");
  }

  // ==========================
  // ADD SECTION
  // ==========================
  $("#addSectionForm").submit(function (e) {
    e.preventDefault();
    $.post("../ajax/add_section.php", $(this).serialize(), function (res) {
      if (res.success) {
        showToast("✅ Section added successfully", "success");
        $("#addSectionModal").modal("hide");
        refreshSections();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to add section", "error");
      }
    }, "json");
  });

  // DELETE SECTION
  $(document).on("click", ".delete-section", function () {
    const id = $(this).data("id");
    $.get("../ajax/delete_section.php", { id }, function (res) {
      if (res.success) {
        showToast("🗑️ Section deleted successfully", "success");
        refreshSections();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to delete section", "error");
      }
    }, "json");
  });

  function refreshSections() {
    $.get("../ajax/get_sections.php", function (res) {
      if (res.success) {
        // Refresh dropdown
        const select = $("#section_id");
        select.empty().append('<option value="">-- Select Section --</option>');
        res.data.forEach(sec => {
          select.append(`<option value="${sec.id}">${sec.section_name}</option>`);
        });

        // Refresh manage modal list
        const list = $("#sectionsList");
        list.empty();
        res.data.forEach(sec => {
          list.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
              ${sec.section_name}
              <button class="btn btn-sm btn-danger delete-section" data-id="${sec.id}">Delete</button>
            </li>
          `);
        });
      }
    }, "json");
  }

  // ==========================
  // ADD PROFESSOR + SUBJECT
  // ==========================
  $("#addProfessorSubjectForm").submit(function (e) {
    e.preventDefault();
    $.post("../ajax/add_professor.php", $(this).serialize(), function (res) {
      if (res.success) {
        showToast("✅ Professor & Subject added successfully", "success");
        $("#addProfessorSubjectModal").modal("hide");
        refreshProfessorSubjects();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to add professor & subject", "error");
      }
    }, "json");
  });

  // DELETE PROFESSOR + SUBJECT
  $(document).on("click", ".delete-professor-subject", function () {
    const id = $(this).data("id");
    $.get("../ajax/delete_professor.php", { id }, function (res) {
      if (res.success) {
        showToast("🗑️ Professor & Subject deleted successfully", "success");
        refreshProfessorSubjects();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to delete professor & subject", "error");
      }
    }, "json");
  });

  function refreshProfessorSubjects() {
    $.get("../ajax/get_professor_subjects.php", function (res) {
      if (res.success) {
        // Refresh dropdown
        const select = $("#professor_subject_id");
        select.empty().append('<option value="">-- Select Professor & Subject --</option>');
        res.data.forEach(ps => {
          select.append(`<option value="${ps.id}">${ps.professor_name} - ${ps.subject_code} (${ps.subject_name})</option>`);
        });

        // Refresh manage modal list
        const list = $("#professorSubjectsList");
        list.empty();
        res.data.forEach(ps => {
          list.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
              ${ps.professor_name} - ${ps.subject_code} (${ps.subject_name})
              <button class="btn btn-sm btn-danger delete-professor-subject" data-id="${ps.id}">Delete</button>
            </li>
          `);
        });
      }
    }, "json");
  }

  // ==========================
  // SUBJECTS (NEW FEATURE)
  // ==========================
  $("#addSubjectForm").submit(function (e) {
    e.preventDefault();
    $.post("../ajax/add_subject.php", $(this).serialize(), function (res) {
      if (res.success) {
        showToast("✅ Subject added successfully", "success");
        $("#addSubjectModal").modal("hide");
        refreshSubjects();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to add subject", "error");
      }
    }, "json");
  });

  $(document).on("click", ".delete-subject", function () {
    const id = $(this).data("id");
    $.get("../ajax/delete_subject.php", { id }, function (res) {
      if (res.success) {
        showToast("🗑️ Subject deleted successfully", "success");
        refreshSubjects();
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(res.message || "❌ Failed to delete subject", "error");
      }
    }, "json");
  });

  function refreshSubjects() {
    $.get("../ajax/get_subjects.php", function (res) {
      if (res.success) {
        // Update professor form subject dropdown
        const subjectSelect = $("#addProfessorSubjectForm select[name='subject_id']");
        subjectSelect.empty();
        res.data.forEach(sub => {
          subjectSelect.append(`<option value="${sub.id}">${sub.subject_code} - ${sub.subject_name}</option>`);
        });

        // Refresh manage modal list
        const list = $("#subjectsList");
        list.empty();
        res.data.forEach(sub => {
          list.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
              ${sub.subject_code} - ${sub.subject_name}
              <button class="btn btn-sm btn-danger delete-subject" data-id="${sub.id}">Delete</button>
            </li>
          `);
        });
      }
    }, "json");
  }

  // ==========================
  // AUTO-REFRESH MODALS WHEN OPENED
  // ==========================
  $('#manageLabsModal').on('shown.bs.modal', refreshLabs);
  $('#manageSectionsModal').on('shown.bs.modal', refreshSections);
  $('#manageProfessorSubjectsModal').on('shown.bs.modal', refreshProfessorSubjects);
  $('#manageSubjectsModal').on('shown.bs.modal', refreshSubjects);
});