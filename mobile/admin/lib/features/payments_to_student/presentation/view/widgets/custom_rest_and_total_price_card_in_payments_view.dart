import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_contain_rest_and_total_price_card_in_payments_view.dart';

class CustomRestAndTotalPriceCardInPaymentsView extends StatelessWidget {
  const CustomRestAndTotalPriceCardInPaymentsView({
    super.key,
    required this.firstText,
    required this.color,
    required this.secondText,
  });
  final String firstText, secondText;
  final Color color;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return Container(
      width: size.width * 0.223,
      padding: SymmetricPaddingWithoutChild.horizontal9AndVertical3(
        context: context,
      ),
      decoration:
          BoxDecorations.boxDecorationToRestAndTotalPriceCardInPaymentsView(
            context: context,
            color: color,
          ),
      child: CustomContainRestAndTotalPriceCardInPaymentsView(
        firstText: firstText,
        secondText: secondText,
      ),
    );
  }
}
