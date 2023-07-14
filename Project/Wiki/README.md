# OnlyFEUP

The main goal of the OnlyFEUP project is the development of a web-based social network with the purpose of creating connections between students and staff, sharing resources about courses and subjects. This is a tool that can be used by anyone from FEUP. After signing up and verifying the user is related to the university (students/teachers), they can start using it for a better experience at FEUP.

A team of administrators is defined, which will be responsible for managing the system, ensuring it runs smoothly, removing illegal content and material in which they are not the author or have permission to share.

This application allows users to integrate into groups and follow students/teachers whom they find their work interesting, they can also create groups if none was found. Users will be able to more easily share resources, invitations to events and workshops, etc with people who are actually interested (their followers).

Users are separated into groups with different permissions. These groups include the above-mentioned administrators, with access and modification privileges, student users and teacher (FEUP staff) users.

The platform will have an adaptive design, allowing users to have a pleasant browsing experience. The product will also provide easy navigation and an excellent overall user experience.

## Project Components

* [ER: Requirements Specification](./er.md)
* [EBD: Database Specification](./edb.md)
* [EAP: Architecture Specification and Prototypes](./eap.md)
* [PA: Product and Presentation](./pa.md)

## Checklists

*  [OnlyFEUP Artifacts Checklist](../docs/OnlyFEUP_Checklist.pdf)
*  [OnlyFEUP Accessibility Checklist](../docs/Accessibility%20Checklist.pdf)
*  [OnlyFEUP Usability Checklist](../docs/Usability%20Checklist.pdf)
*  [OnlyFEUP CSS Validation](../docs/CSS%20Validation.pdf)
*  [OnlyFEUP HTML Validation](../docs/HTML%20Validation.pdf)
## Product

The final version was available in https://lbaw2255.lbaw.fe.up.pt/

```code
docker run -it -p 8000:80 --name=lbaw2255 -e DB_DATABASE="lbaw2255" -e DB_SCHEMA="lbaw2255" -e DB_USERNAME="lbaw2255" -e DB_PASSWORD="reWisDQE" git.fe.up.pt:5050/lbaw/lbaw2223/lbaw2255
```

### Credentials

- eduardanascimento@gmail.com / eduardalbaw2255 (Administrator)
- laravel@hotmail.com / password (Normal Account)

## Team

* André Correia da Costa (up201905916@fe.up.pt)
* Fábio Araújo de Sá (up202007658@fe.up.pt)
* Lourenço Alexandre Correia Gonçalves (up202004816@fe.up.pt)
* Marcos William Ferreira Pinto (up201800177@fe.up.pt)

---

GROUP lbaw 2255, 25/09/2022 - 02/01/2023