const app = {
  get: function (link, data, token) {
    const startTime = Date.now(); // Bắt đầu đếm thời gian
    return $.ajax({
      url: link,
      type: "GET",
      data: data,
      headers: {
        Authorization: "Bearer " + token,
      },
      success: function (response) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.log("GET request successful:", response);
        console.log("Time taken:", endTime - startTime + " ms");
      },
      error: function (error) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.error("GET request failed:", error);
        console.log("Time taken:", endTime - startTime + " ms");
      },
    });
  },

  post: function (link, data, token) {
    const startTime = Date.now(); // Bắt đầu đếm thời gian
    return $.ajax({
      url: link,
      type: "POST",
      data: JSON.stringify(data),
      contentType: "application/json",
      headers: {
        Authorization: "Bearer " + token,
      },
      success: function (response) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.log("POST request successful:", response);
        console.log("Time taken:", endTime - startTime + " ms");
      },
      error: function (error) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.error("POST request failed:", error);
        console.log("Time taken:", endTime - startTime + " ms");
      },
    });
  },

  patch: function (link, data, token) {
    const startTime = Date.now(); // Bắt đầu đếm thời gian
    return $.ajax({
      url: link,
      type: "PATCH",
      data: JSON.stringify(data),
      contentType: "application/json",
      headers: {
        Authorization: "Bearer " + token,
      },
      success: function (response) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.log("PATCH request successful:", response);
        console.log("Time taken:", endTime - startTime + " ms");
      },
      error: function (error) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.error("PATCH request failed:", error);
        console.log("Time taken:", endTime - startTime + " ms");
      },
    });
  },

  remove: function (link, token) {
    const startTime = Date.now(); // Bắt đầu đếm thời gian
    return $.ajax({
      url: link,
      type: "DELETE",
      headers: {
        Authorization: "Bearer " + token,
      },
      success: function (response) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.log("DELETE request successful:", response);
        console.log("Time taken:", endTime - startTime + " ms");
      },
      error: function (error) {
        const endTime = Date.now(); // Kết thúc đếm thời gian
        console.error("DELETE request failed:", error);
        console.log("Time taken:", endTime - startTime + " ms");
      },
    });
  },

  popup: function (title, view) {
    $("body").append(`
          <div class="bg_popup">
              <div class="detectOut"></div>
              <div class="main-view">
                  <div class="whitebox">
                      <div class="header">
                          <div class="title">${title}</div>
                      </div>
                      <div class="body">${view}</div>
                  </div>
              </div>
          </div>    
      `);
    $(".detectOut").click(function () {
      this.parentNode.remove();
    });
  },

  closePopup: function () {
    $(".bg_popup").remove();
  },

  getCookie: function (name) {
    // Tạo chuỗi tìm kiếm cookie có dạng "name="
    const nameEQ = name + "=";
    // Lấy tất cả cookies từ document.cookie
    const cookies = document.cookie.split(";");

    // Duyệt qua từng cookie
    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i];
      // Xóa khoảng trắng ở đầu cookie
      while (cookie.charAt(0) === " ") {
        cookie = cookie.substring(1, cookie.length);
      }
      // Nếu cookie bắt đầu bằng "name="
      if (cookie.indexOf(nameEQ) === 0) {
        // Trả về giá trị của cookie
        return cookie.substring(nameEQ.length, cookie.length);
      }
    }
    // Nếu không tìm thấy cookie, trả về null
    return null;
  },
};
