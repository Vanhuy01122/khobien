document.getElementById("button_city").addEventListener("click", function () {
  var selectedValue = document.getElementById("list_city").value;
  var url_goc = window.location.href;
  if (selectedValue) {
    let redirect = url_goc + "danh-muc-san-pham/" + selectedValue; // Thay đổi URL cho phù hợp
    window.location.href = redirect;
  } else {
    alert("Vui lòng chọn một tỉnh/thành phố!");
  }
});
