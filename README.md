# headless_gridelements
This extension makes it possible to use [EXT:container](https://github.com/b13/container) with
[EXT:headless](https://github.com/TYPO3-Initiatives/headless/).

## Installation

### Installation with [`composer`](https://getcomposer.org/) (recommended)
```shell script
$ composer require itplusx/headless-container
```

### Installation with the TYPO3 Extension Manager
see: [Extension Management](https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/ExtensionArchitecture/HowTo/ExtensionManagement.html#extension-management)

## Requirements and compatibility
| Requirement                                            | Version  |
|--------------------------------------------------------|----------|
| PHP                                                    | 7.4, 8.0 |
| TYPO3                                                  | 11.5     |
| [Headless](https://github.com/TYPO3-Headless/headless) | 3        |
| [Container](https://github.com/b13/container)          | 2        |

## Usage
1. [Include TypoScript](https://docs.typo3.org/m/typo3/reference-typoscript/11.5/en-us/UsingSetting/Entering.html#include-typoscript-from-extensions)
2. Register your custom container element (see: [Registration of Container Elements](https://github.com/b13/container/tree/2.0.5#registration-of-container-elements))
3. Define TypoScript for custom container element ...
   * ... for simple container elements:
     ```
     tt_content.b13-2cols-with-header-container < lib.container
     ```
   * ... for container elements with header TCA fields:
     ```
     tt_content.b13-2cols-with-header-container < lib.containerWithHeader
     ```
   * ... for container elements with custom TCA fields (see: [Create custom content elements](https://docs.typo3.org/p/friendsoftypo3/headless/3.1/en-us/Developer/Index.html#create-custom-content-elements)):
     ```
     tt_content.b13-2cols-with-header-container < lib.container
     tt_content.b13-2cols-with-header-container.fields.content.fields {
       myCustomField = TEXT
       myCustomField.field = myCustomTcaField
     }
     ```
4. That's all! You should now see JSON output for your custom container element!

## Example json output
```json
{
  "id": 2,
  "type": "b13-2cols-with-header-container",
  "colPos": 0,
  "categories": "",
  "appearance": {
    "layout": "default",
    "frameClass": "default",
    "spaceBefore": "",
    "spaceAfter": ""
  },
  "content": {
    "header": "Container Header",
    "subheader": "Container Subheader",
    "headerLayout": 0,
    "headerPosition": "",
    "headerLink": "",
    "myCustomField": "myCustomValue",
    "gridItems": [
      {
        "config": {
          "name": "header",
          "colPos": 200
        },
        "children": [
          {"id": 3, "type": "text", "colPos": 200, "categories": "", "appearance": { ... }, "content": { ... } }
        ]
      },
      {
        "config": {
          "name": "left side",
          "colPos": 201
        },
        "children": [
          {"id": 4, "type": "text", "colPos": 201, "categories": "", "appearance": { ... }, "content": { ... } },
          {"id": 9, "type": "text", "colPos": 201, "categories": "", "appearance": { ... }, "content": { ... } }
        ]
      },
      {
        "config": {
          "name": "right side",
          "colPos": 202
        },
        "children": [
          {"id": 5, "type": "text", "colPos": 202, "categories": "", "appearance": { ... }, "content": { ... } }
        ]
      }
    ]
  }
}
```

## Contribution
Any help on this project is very welcome! May it be as code contribution or just an idea for improvement. But we would
like to ask you to follow some rules:

- **Issues:**  
  When adding issues please always describe the bug/feature/task as detailed as possible. Only providing a title is not
  enough. Please use issue templates.
- **Commits:**  
  Our team is following the [Conventional Commits](https://www.conventionalcommits.org/). We would like ask you stick to
  these rules whenever you want to contribute.
- **Pull Requests:**  
  Before you submit a PR please create an issue first and link it to the pull request or at least add a PR description 
  with detailed information about what this PR does. Otherwise we are not able to decide if this PR is worth going into
  the main branch.

---

<p align="center">
  <a href="https://itplusx.de" target="_blank" rel="noopener noreferrer">
    <img width="350" src="https://itplusx.de/banners/created-by-X-with-passion.svg" alt="ITplusX - Internetagentur & Systemhaus">
  </a>
</p>

---
