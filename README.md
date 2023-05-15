# Scandiweb - Recruitment Task - Backend

<img src="https://github.com/laravel/sanctum/workflows/tests/badge.svg" alt="Build Status" style="max-width: 100%;">

## Demo
[scandiweb.justeuro.eu](https://scandiweb.justeuro.eu:8080)

## Introduction
This is an API created for a product management system. It includes three endpoints:
| Purpose                    | Endpoint                                                              |
|----------------------------|-----------------------------------------------------------------------|
| 1. Adding a new product    | [POST /api/product/saveApi](#endpoint-1-post-apiproductsaveapi)       |
| 2. Retrieving product(s)   | [GET /api/product/get](#endpoint-2-get-apiproductget)                 |
| 3. Bulk delete of products | [POST /api/product/bulkDelete](#endpoint-3-post-apiproductbulkdelete) |

<br>

## Endpoint 1: POST /api/product/saveApi

This endpoint is used to add new product to the database. The request should be sent with the following properties:

- `sku`: The product SKU (required, string)
- `name`: The product name (required, string)
- `price`: The product price (required, decimal)
- `productType`: The product type, should be one of `dvd`, `book`, or `furniture` (required, string)
- `size`: The product size (in MB), required if the product type is `dvd` (int)
- `weight`: The product weight (in Kg), required if the product type is `book` (int)
- `height`: The product height (in cm), required if the product type is `furniture` (int)
- `width`: The product width (in cm), required if the product type is `furniture` (int)
- `length`: The product length (in cm), required if the product type is `furniture` (int)

<details>
<summary><font size="5"><b>View Response</b></font></summary>

If any of the required properties are missing or have an incorrect data type, the API will return a JSON object with the following format:


```json
{
    "message": "Validation Failed",
    "error": {
        "inputName": [
            "Message indicating what happened."
        ]
    }
}
```

If the product is successfully added, the API will return a JSON object with the following format:


```json
{
    "message": "Success"
}
```
</details>

<br>

## Endpoint 2: GET /api/product/get

This endpoint is used to retrieve the details of all products or of a single product. To get a single product, the `sku` parameter is required in the query string, like this: `/product/get?sku=ProductSku100`. If the product is found, the API will return a JSON object with the following format:

```json
{
    "id": 1,
    "sku": "ProductSku100",
    "name": "ProductName",
    "price": "100.00",
    "size": 700,
    "weight": null,
    "height": null,
    "width": null,
    "length": null
}
```

If sku parameter is not present, the API will return all products:

```json
[
  {
      "id": 1,
      "sku": "ProductSku100",
      "name": "ProductName",
      "price": "100.00",
      "size": 700,
      "weight": null,
      "height": null,
      "width": null,
      "length": null
  },
  {
      "id": 2,
      "sku": "ProductSku102",
      "name": "ProductName",
      "price": "100.00",
      "size": 700,
      "weight": null,
      "height": null,
      "width": null,
      "length": null
  },
]
```

<br>

## Endpoint 3: POST /api/product/bulkDelete

This endpoint is used to delete product(s). The request should be sent with the `ids` parameter containing array of ids to be deleted.

If the product or products are successfully deleted, the API will return a JSON object with the following format:

```json
{
    "message": "Success"
}
```

If `ids` parameter is not present, the API will return an error:

```json
{
    "message": "Validation Failed",
    "error": {
        "ids": [
            "The ids field is required."
        ]
    }
}
```

<br>

## Author
[Tymoteusz Krysta](https://www.linkedin.com/in/tim-krysta/) - krystatymoteusz@gmail.com

<br>

## Final provisions
The Frontend is located here: [https://github.com/timkrysta/scandiweb-frontend](https://github.com/timkrysta/scandiweb-frontend)
