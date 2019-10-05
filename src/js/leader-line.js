function init() {
  const links = document.querySelectorAll("[data-links-to]");
  if (links.length > 0) {
    for (let i = 0; i < links.length; i++) {
      if (links[i].dataset.linksTo.length < 1) {
        continue;
      }
      const linksTo = JSON.parse(links[i].dataset.linksTo);
      if (typeof linksTo == "object") {
        for (let n = 0; n < linksTo.length; n++) {
          var line = new LeaderLine(
            links[i],
            document.getElementById(linksTo[n]),
            {}
          );
          // line.id = i + "-" + n;
          // line.id = links[i].id + "--" + linksTo[n];
          // console.log(line.id, line);
          // console.log(document.getElementById(line.id));
          if (
            links[i].id == "sequence-selvstendighet" &&
            document.getElementById(linksTo[n]).id ==
              "sequence-endringer-relasjoner"
          ) {
            // console.log(
            //   "sequence-selvstendighet to sequence-endringer-relasjoner",
            //   line,
            //   line.id,
            //   line._id
            // );
            // console.log(
            //   "LINE_Y: ",
            //   links[i].getBoundingClientRect().top +
            //     window.pageYOffset +
            //     links[i].getBoundingClientRect().height / 2
            // );
            line.color = "rgba(30, 130, 250, 0.5)";
            /* line.setOptions({
              // startSocket: "bottom",
              // endSocket: "left",
              startSocketGravity: [192, -172],
              endSocketGravity: [-192, -172]
            }); */
          }
        }
      }
    }

    const sections = document.querySelectorAll(
      ".sequence main article [role=list]"
    );
    if (sections.length > 0) {
      for (let i = 0; i < sections.length; i++) {
        const dimensions = sections[i].getBoundingClientRect();
        const computedStyles = window.getComputedStyle(sections[i]);
        var shadow = document.createElement("div");
        shadow.style.position = "absolute";
        shadow.style.top = dimensions.top + "px";
        shadow.style.right = dimensions.right + "px";
        shadow.style.bottom = dimensions.bottom + "px";
        shadow.style.left = dimensions.left + "px";
        shadow.style.width = dimensions.width + "px";
        shadow.style.height = dimensions.height + "px";
        shadow.style.backgroundColor = computedStyles.getPropertyValue(
          "background-color"
        );
        sections[i].style.backgroundColor = "transparent";
        document.querySelector(".sequence main article").append(shadow);
      }
    }
    document.querySelectorAll(".leader-line").forEach(line => {
      document.querySelector(".sequence main article").append(line);
    });
  }
}

export { init as leaderLineInit };
