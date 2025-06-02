document.addEventListener("DOMContentLoaded", function () {
	const menuToggle = document.querySelector(".menu-toggle");
	const menuContainer = document.querySelector(".menu-container");
	const subMenuParents = document.querySelectorAll(".menu-item-has-children");

	// Toggle main menu
	menuToggle.addEventListener("click", function () {
		const isExpanded = this.getAttribute("aria-expanded") === "true";
		this.setAttribute("aria-expanded", !isExpanded);
		this.classList.toggle("is-active");
		menuContainer.classList.toggle("is-active");
		document.body.style.overflow = isExpanded ? "" : "hidden";
	});

	// Handle submenu toggles
	subMenuParents.forEach((parent) => {
		const link = parent.querySelector("a");
		const subMenu = parent.querySelector(".sub-menu");

		// Set initial state
		link.setAttribute("aria-expanded", "false");

		// Make parent menu item clickable for toggle
		link.addEventListener("click", function (e) {
			e.preventDefault();
			const isExpanded = this.getAttribute("aria-expanded") === "true";
			this.setAttribute("aria-expanded", !isExpanded);

			if (isExpanded) {
				// Collapsing
				subMenu.style.height = subMenu.scrollHeight + "px";
				// Force reflow
				subMenu.offsetHeight;
				subMenu.style.height = "0px";
				subMenu.classList.remove("is-active");
			} else {
				// Expanding
				subMenu.style.height = "0px";
				subMenu.classList.add("is-active");
				// Force reflow
				subMenu.offsetHeight;
				subMenu.style.height = subMenu.scrollHeight + "px";
			}

			// Clean up after animation
			subMenu.addEventListener("transitionend", function handler() {
				if (!isExpanded) {
					subMenu.style.height = "auto";
				}
				subMenu.removeEventListener("transitionend", handler);
			});
		});
	});

	// Close menu when clicking outside
	document.addEventListener("click", function (e) {
		if (!menuContainer.contains(e.target) && !menuToggle.contains(e.target)) {
			menuToggle.setAttribute("aria-expanded", "false");
			menuToggle.classList.remove("is-active");
			menuContainer.classList.remove("is-active");
			document.body.style.overflow = "";
		}
	});

	// Handle keyboard navigation
	menuContainer.addEventListener("keydown", function (e) {
		if (e.key === "Escape") {
			menuToggle.setAttribute("aria-expanded", "false");
			menuToggle.classList.remove("is-active");
			menuContainer.classList.remove("is-active");
			document.body.style.overflow = "";
			menuToggle.focus();
		}
	});
});
