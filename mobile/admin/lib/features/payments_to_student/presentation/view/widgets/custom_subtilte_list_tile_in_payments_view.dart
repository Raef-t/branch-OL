import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_date_in_subtitle_list_tile_in_payments_view.dart';
import '/gen/fonts.gen.dart';

class CustomSubtilteListTileInPaymentsView extends StatelessWidget {
  const CustomSubtilteListTileInPaymentsView({
    super.key,
    required this.date,
    required this.financialInvoice,
    required this.paymentType,
  });
  final String date, financialInvoice, paymentType;
  @override
  Widget build(BuildContext context) {
    return paymentType == 'green'
        ? Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CustomDateInSubtitleListTileInPaymentsView(date: date),
              Heights.height6(context: context),
              TextMedium12Component(
                text: 'رقم الإيصال:$financialInvoice',
                fontFamily: FontFamily.tajawal,
                color: ColorsStyle.greyColor,
              ),
            ],
          )
        : const SizedBox();
  }
}
