import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/many_of_card_and_two_texts_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/sized_boxs/heights.dart';
import '/features/details_students/presentation/view/widgets/custom_header_details_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_rating_card_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_see_more_text_with_another_text_and_exams_card_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_see_more_text_with_another_text_and_payments_card_in_details_student_view.dart';

class CustomSliverFillRemainingInDetailsStudentView extends StatelessWidget {
  const CustomSliverFillRemainingInDetailsStudentView({
    super.key,
    required this.selectedValue,
    required this.maxRating,
    required this.onSelected,
  });
  final String selectedValue;
  final double maxRating;
  final void Function(String) onSelected;
  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            const CustomHeaderDetailsInDetailsStudentView(),
            Heights.height20(context: context),
            ManyOfCardAndTwoTextsComponent(
              messageOnTap: () {},
              attendanceOnTap: () {
                pushGoRouterHelper(
                  context: context,
                  view: kAttendanceViewRouter,
                );
              },
              paymentOnTap: () {
                pushGoRouterHelper(context: context, view: kPaymentsViewRouter);
              },
              workHourOnTap: () => pushGoRouterHelper(
                context: context,
                view: kWorkHoursToStudentViewRouter,
              ),
              markOnTap: () {
                pushGoRouterHelper(context: context, view: kExamsView2Router);
              },
            ),
            Heights.height29(context: context),
            CustomRatingCardInDetailsStudentView(
              selectedValue: selectedValue,
              maxRating: maxRating,
              onSelected: onSelected,
            ),
            Heights.height30(context: context),
            const CustomSeeMoreTextWithAnotherTextAndPaymentsCardInDetailsStudentView(),
            Heights.height24(context: context),
            const CustomSeeMoreTextWithAnotherTextAndExamsCardInDetailsStudentView(),
          ],
        ),
      ),
    );
  }
}
