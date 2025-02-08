import { computePosition, autoPlacement, autoUpdate } from "blockstudio/@floating-ui/dom@1.6.13";

document.addEventListener("DOMContentLoaded", () => {

    if (!document.body.classList.contains("admin-bar")) {
        console.warn("Skipping toolbar initialization: 'admin-bar' class not found on body.");
        return;
    }

    const homeLink = `${window.location.origin}/wp-admin/site-editor.php`;
    const textDomain = "wp2";

    // Get and sort template parts by vertical position
    const templateParts = [...document.querySelectorAll(".wp-block-template-part")]
        .sort((a, b) => a.getBoundingClientRect().top - b.getBoundingClientRect().top);

    templateParts.forEach((part, index) => {
        const templateArea = part.getAttribute("data-wp2-template-area");
        const templateZone = part.getAttribute("data-wp2-template-zone");
        const template = part.getAttribute("data-wp2-template");

        if (!templateArea || !templateZone || !template) return;

        const templatePartId = `${textDomain}//${templateZone}-${templateArea}-part-${template}`;

        part.setAttribute("data-template-part-id", templatePartId);

        part.setAttribute("data-wp2-template-part-position", index + 1);

        const encodedPostId = encodeURIComponent(templatePartId);

        const editUrl = `${homeLink}?postId=${encodedPostId}&postType=wp_template_part&focusMode=true&canvas=edit`;

        const toolbar = document.createElement("div");

        toolbar.classList.add("wp2-template-part-toolbar");

        const editButton = document.createElement("a");

        editButton.href = editUrl;

        editButton.textContent = "Edit";

        editButton.target = "_blank";

        toolbar.appendChild(editButton);

        part.appendChild(toolbar);

        const updatePosition = async () => {
            const { x, y } = await computePosition(part, toolbar, {
                placement: "top-end",
                middleware: [autoPlacement({ allowedPlacements: ['top-end'] })],
            });
        };

        autoUpdate(part, toolbar, updatePosition);

        part.addEventListener("mouseenter", () => {
            toolbar.style.display = "block";
            updatePosition();
        });

        part.addEventListener("mouseleave", () => {
            toolbar.style.display = "none";
        });
    });
});