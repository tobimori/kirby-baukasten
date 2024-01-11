# src > controllers

This folder contains controller files for [https://stimulus.hotwired.dev/](Stimulus) framework. They will be autoloaded by a script included in index.ts.

The naming conventions are different from the Stimulus default. Please do not append `*_controller.ts` at the end of the filename. Instead:

- Use hyphens to separate words
- Just use your desired controller name as the filename

→ `hello-world.ts`, not `hello_world_controller.ts`

## Lazy loading

When importing large libraries, it is recommended to lazy load them. This can be done by appending `.lazy` to the filename. Thanks to ES Modules, the file will then be loaded only when the controller is used for the first time.

→ `hello-world.lazy.ts`

## TypeScript

We use [stimulus-typescript](https://github.com/ajaishankar/stimulus-typescript) for strongly typed Stimulus controllers.
