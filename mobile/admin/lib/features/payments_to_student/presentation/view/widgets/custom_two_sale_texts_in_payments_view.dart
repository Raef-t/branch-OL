import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/enrollment_contract_model.dart';
import '/gen/fonts.gen.dart';

class CustomTwoSaleTextsInPaymentsView extends StatelessWidget {
  const CustomTwoSaleTextsInPaymentsView({
    super.key,
    required this.enrollmentContractModel,
  });
  final EnrollmentContractModel? enrollmentContractModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium16Component(
      text:
          '%${enrollmentContractModel?.discountPercentage} '
          'نسبة الاسفادة من الحسم',
      textAlign: TextAlign.center,
      fontFamily: FontFamily.inter,
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
