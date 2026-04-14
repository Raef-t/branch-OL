import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomSubtitlePaymentsListTileInDetailsStudentView
    extends StatelessWidget {
  const CustomSubtitlePaymentsListTileInDetailsStudentView({
    super.key,
    required this.lastElementInListOfPaymentModel,
  });
  final PaymentModel lastElementInListOfPaymentModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Assets.images.dateImage.image(),
        Widths.width11(context: context),
        TextMedium12Component(
          text: lastElementInListOfPaymentModel.date ?? 'لا يوجد',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.greyColor,
        ),
      ],
    );
  }
}
