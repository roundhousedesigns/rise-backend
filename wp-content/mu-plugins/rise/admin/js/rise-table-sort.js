/**
 * Rise Admin Table sort order
 */

document.addEventListener("DOMContentLoaded", function () {
	// Function to toggle the sort direction
	function toggleSortDirection(header) {
		var currentDir = header.dataset.sortDir;
		var newDir = currentDir === "asc" ? "desc" : "asc";
		header.dataset.sortDir = newDir;
		header.classList.toggle("asc", newDir === "asc");
		header.classList.toggle("desc", newDir === "desc");
		header.querySelector(".sort-indicator").textContent = newDir === "asc" ? "▲" : "▼";
		return newDir; // Return the new sort direction
	}

	// Function to perform the sorting
	function sortTable(table, columnIndex, sortDirection) {
		var tbody = table.querySelector("tbody");
		var rows = Array.from(tbody.querySelectorAll("tr"));

		rows.sort(function (a, b) {
			var aValue = a.querySelectorAll("td")[columnIndex].textContent.trim();
			var bValue = b.querySelectorAll("td")[columnIndex].textContent.trim();

			// Numeric sorting
			var aNum = parseFloat(aValue);
			var bNum = parseFloat(bValue);

			if (!isNaN(aNum) && !isNaN(bNum)) {
				aValue = aNum;
				bValue = bNum;
			}

			if (aValue === bValue) {
				return 0;
			} else if (sortDirection === "asc") {
				return aValue < bValue ? -1 : 1;
			} else {
				return aValue > bValue ? -1 : 1;
			}
		});

		rows.forEach(function (row) {
			tbody.appendChild(row);
		});
	}

	// Function to create the sort indicator icons
	function createSortIndicator(sortDirection) {
		var indicator = document.createElement("span");
		indicator.classList.add("sort-indicator");
		indicator.textContent = sortDirection === "asc" ? "▲" : "▼"; // Set initial arrow direction
		return indicator;
	}

	// Event handler for sorting
	document.addEventListener("click", function (event) {
		var target = event.target;
		if (target.classList.contains("sort")) {
			var header = target;
			var table = header.closest("table");
			var columnIndex = Array.from(header.parentNode.cells).indexOf(header);

			var sortDirection = toggleSortDirection(header); // Get the new sort direction directly

			sortTable(table, columnIndex, sortDirection);
		}
	});

	// Add sort indicators to the column headers
	var headers = document.querySelectorAll("th.sort");
	headers.forEach(function (header) {
		var sortDirection = header.dataset.sortDir || "asc";
		var sortIndicator = createSortIndicator(sortDirection);
		header.appendChild(sortIndicator);
	});
});
