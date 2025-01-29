/**
 * Dashboard Post Report
 */

'use strict';

// Datatable (jquery)
$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  // Variable declaration for table
  var dt_user_table = $('.datatables-users'),
    select2 = $('.select2'),
    userView = baseUrl + '',
    statusObj = {
      1: { title: 'Block', class: 'bg-label-danger' },
      2: { title: 'Active', class: 'bg-label-success' }
    };

  if (select2.length) {
    var $this = select2;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select Country',
      dropdownParent: $this.parent()
    });
  }

  dt_user_table.before('<h5 style="margin-top: 1rem !important; margin-left:1rem;" class="">Recent Posts Report</h5>');
  // Users datatable
  if (dt_user_table.length) {
    var dt_user = dt_user_table.DataTable({
      serverSide: true,
      processing: true,
      searching: false,
      lengthMenu: [5],
      ajax: baseUrl + 'getpostreportdata',
      columns: [
        { data: 'id' },
        { data: 'created_at' },
        { data: 'blockedPostPic' },
        { data: 'blockedByUserName' },
        { data: 'postUserName' },
        { data: 'report_text' },
        { data: 'action' }
      ],
      columnDefs: [
        { visible: false, targets: [0] },
        {
          // Date
          targets: 1,
          render: function (data, type, full, meta) {
            var $created_at = full['created_at'];

            return '<span class="fw-medium">' + $created_at + '</span>';
          }
        },
        {
          // Post Photo
          targets: 2,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $image = full['blockedPostPic']; // Declare $image using var, let, or const

            if ($image) {
              // For Avatar image
              var $output =
                '<img src="' +
                assetsPath +
                'images/posts/' +
                $image +
                '" alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px;">';
            } else {
              // If $image is not available, you may want to handle this case accordingly
              var $output = '';
            }

            return $output;
          }
        },
        {
          // Username
          targets: 3,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $username = full['blockedByUserName'],
              $email = full['blockedByUserEmail'],
              $image = full['blockedByUserPic'];
            if ($image) {
              // For Avatar image
              var $output =
                '<img src="' + assetsPath + 'images/user/' + $image + '" alt="Avatar" class="rounded-circle">';
            } else {
              // For Avatar badge
              var stateNum = Math.floor(Math.random() * 6);
              var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
              var $state = states[stateNum],
                $username = full['blockedByUserName'],
                $initials = $username.match(/\b\w/g) || [];
              $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
              $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
            }
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="avatar-wrapper">' +
              '<div class="avatar avatar-sm me-3">' +
              $output +
              '</div>' +
              '</div>' +
              '<div class="d-flex flex-column">' +
              '<span class="fw-medium">' +
              $username +
              '</span>' +
              '<small class="text-muted">' +
              $email +
              '</span>' +
              '<small class="text-muted">' +
              '</div>';
            return $row_output;
          }
        },
        {
          // Username
          targets: 4,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $username = full['postUserName'],
              $email = full['postUserEmail'],
              $image = full['postUserPic'];
            if ($image) {
              // For Avatar image
              var $output =
                '<img src="' + assetsPath + 'images/user/' + $image + '" alt="Avatar" class="rounded-circle">';
            } else {
              // For Avatar badge
              var stateNum = Math.floor(Math.random() * 6);
              var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
              var $state = states[stateNum],
                $username = full['postUserName'],
                $initials = $username.match(/\b\w/g) || [];
              $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
              $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
            }
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="avatar-wrapper">' +
              '<div class="avatar avatar-sm me-3">' +
              $output +
              '</div>' +
              '</div>' +
              '<div class="d-flex flex-column">' +
              '<span class="fw-medium">' +
              $username +
              '</span>' +
              '<small class="text-muted">' +
              $email +
              '</span>' +
              '<small class="text-muted">' +
              '</div>';
            return $row_output;
          }
        },
        {
          // Report Text
          targets: 5,
          render: function (data, type, full, meta) {
            var $report_text = full['report_text'];

            return '<span class="fw-medium">' + $report_text + '</span>';
          }
        },
        {
          // Actions
          targets: 6,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            var userId = full.id;
            return (
              '<div class="d-inline-block text-nowrap">' +
              '<button class="btn btn-sm btn-icon delete-record" onclick="deletePostReport(' +
              userId +
              ')"><i class="bx bx-trash"></i></button>' +
              '</div>'
            );
          }
        }
      ],

      order: [[0, 'desc']],
      dom: '<"row mx-2"' + '<"col-md-12"<""f>>' + '>t' + '<"row mx-2"' + '<"col-sm-12 col-md-6">' + '>',

      initComplete: function () {
        // Adding role filter once table initialized
        this.api()
          .columns(2)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="UserRole" class="form-select text-capitalize"><option value=""> Select Role </option></select>'
            )
              .appendTo('.user_role')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                if (d !== undefined) {
                  select.append('<option value="' + d + '">' + d + '</option>');
                }
              });
          });
        // Adding plan filter once table initialized
        this.api()
          .columns(3)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="UserPlan" class="form-select text-capitalize"><option value=""> Select Plan </option></select>'
            )
              .appendTo('.user_plan')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                if (d !== undefined) {
                  select.append('<option value="' + d + '">' + d + '</option>');
                }
              });
          });
        // Adding status filter once table initialized
        this.api()
          .columns(5)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="FilterTransaction" class="form-select text-capitalize"><option value=""> Select Status </option></select>'
            )
              .appendTo('.user_status')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                if (statusObj[d] && statusObj[d].title) {
                  select.append(
                    '<option value="' +
                      statusObj[d].title +
                      '" class="text-capitalize">' +
                      statusObj[d].title +
                      '</option>'
                  );
                }
              });
          });
      }
    });

    // To remove default btn-secondary in export buttons
    $('.dt-buttons > .btn-group > button').removeClass('btn-secondary');
  }

  // Delete Record
  $('.datatables-users tbody').on('click', '.delete-record', function () {
    dt_user.row($(this).parents('tr')).remove().draw();
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
});

// Validation & Phone mask
(function () {
  const phoneMaskList = document.querySelectorAll('.phone-mask'),
    addNewUserForm = document.getElementById('addNewUserForm');

  // Phone Number
  if (phoneMaskList) {
    phoneMaskList.forEach(function (phoneMask) {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: 'US'
      });
    });
  }
  // Add New User Form Validation
  const fv = FormValidation.formValidation(addNewUserForm, {
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.mb-3';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });
})();

function deletePostReport(userId) {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert Report!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Delete Report!',
    customClass: {
      confirmButton: 'btn btn-primary me-2',
      cancelButton: 'btn btn-label-secondary'
    },
    buttonsStyling: false
  }).then(function (result) {
    if (result.value) {
      $.ajax({
        url: 'postreport-delete/' + userId,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          var parentTr = $('#report-deleted-' + data.userId).closest('tr');
          parentTr.remove();
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Report deleted successfully.',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          }).then(function () {
            location.reload(); // Reload the page
          });
        }
      });
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      Swal.fire({
        title: 'Cancelled',
        text: 'Cancelled Delete :)',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-success'
        }
      });
    }
  });
}
