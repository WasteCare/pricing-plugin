export async function postForm(url, fields, data) {
  /*
      address: address.value,
      businessName: businessName.value,
      contactName: contactName.value,
      email: email.value,
      existingCustomer: existingCustomer.value,
      marketingOi: marketingOi.value,
      materials: materials.value,
      multipleSites: multipleSites.value,
      phone: phone.value,
      privacyOi: privacyOi.value
    */

  const formattedData = {
    [inputName(fields.address)]: data.address,
    [inputName(fields.businessName)]: data.businessName,
    [inputName(fields.contactName)]: data.contactName,
    [inputName(fields.email)]: data.email,
    [inputName(fields.phone)]: data.phone,
    [inputName(fields.materials)]: materialsToArray(data.materials),
    [inputName(fields.eaFee)]: data.eaFee,
    [inputName(fields.rebate)]: data.rebate,
    [inputName(fields.totalCharge)]: data.totalCharge,
    [inputName(fields.transportRate)]: data.transportCharge,
    [inputName(fields.existingCustomer)]: data.existingCustomer.value,
    [inputName(fields.multipleSites)]: data.multipleSites.value,
    //[inputName(fields.industryType)]: data.industryType?.
    ...acceptToGF(fields.privacyOi, data.privacyOi),
    ...acceptToGF(fields.marketingOi, data.marketingOi)
  }

  console.log(formattedData)

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(formattedData)
  })

  const json = await response.json()
  return json
}

function inputName(field) {
  return `input_${field.id}`
}

function materialsToArray(materials) {
  return materials
    .map((material) => [
      `${material.matKey} : ${material.materialCustomerName}`,
      material.qty,
      material.price
    ])
    .flat()
}

function acceptToGF(field, acceptValue) {
  /*

    'input_8.1': 1,
    'input_8.2': 'I agree to the privacy policy.',
    'input_8.3': 2,

    */

  const input = inputName(field)
  let output = {
    [`${input}.2`]: field.checkboxLabel,
    [`${input}.3`]: 2
  }
  if (acceptValue === true) output[`${input}.1`] = 1

  return output
}
