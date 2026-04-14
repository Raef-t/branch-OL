import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/gen/fonts.gen.dart';

class CustomTrailingPaymentsListTileInDetailsStudentView
    extends StatelessWidget {
  const CustomTrailingPaymentsListTileInDetailsStudentView({
    super.key,
    required this.lastElementInListOfPaymentModel,
  });
  final PaymentModel lastElementInListOfPaymentModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium12Component(
      text:
          r'$'
          '${lastElementInListOfPaymentModel.amount}',
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.mediumBlackColor2,
    );
    // return Column(
    //   children: [

    //     Heights.height8(context: context),
    //     // const TextMedium12Component(
    //     //   text: r'$200 المتبقي',
    //     //   fontFamily: FontFamily.tajawal,
    //     //   color: ColorsStyle.greyColor,
    //     // ),
    //   ],
    // );
  }
}
