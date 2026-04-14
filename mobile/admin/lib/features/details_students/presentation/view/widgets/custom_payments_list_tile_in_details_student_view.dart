import 'package:flutter/material.dart';
import '/core/components/medium_circle_dot_component.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/details_students/presentation/view/widgets/custom_subtitle_payments_list_tile_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_title_payments_list_tile_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_trailing_payments_list_tile_in_details_student_view.dart';

class CustomPaymentsListTileInDetailsStudentView extends StatelessWidget {
  const CustomPaymentsListTileInDetailsStudentView({
    super.key,
    required this.lastElementInListOfPaymentModel,
  });
  final PaymentModel lastElementInListOfPaymentModel;
  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: const MediumCircleDotComponent(color: ColorsStyle.greenColor3),
      title: const CustomTitlePaymentsListTileInDetailsStudentView(),
      subtitle: CustomSubtitlePaymentsListTileInDetailsStudentView(
        lastElementInListOfPaymentModel: lastElementInListOfPaymentModel,
      ),
      trailing: CustomTrailingPaymentsListTileInDetailsStudentView(
        lastElementInListOfPaymentModel: lastElementInListOfPaymentModel,
      ),
    );
  }
}
