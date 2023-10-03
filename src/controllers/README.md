# src > controllers

This folder contains controller files for [https://stimulus.hotwired.dev/](Stimulus) framework. They will be autoloaded by a script included in index.ts.

The naming conventions are different from the Stimulus default. Please do not append `*_controller.ts` at the end of the filename. Instead:

- Use hyphens to separate words
- Just use your desired controller name as the filename

→ `hello-world.ts`, not `hello_world_controller.ts`

TODO: add automatic lazyloading with `.lazy.ts`.
