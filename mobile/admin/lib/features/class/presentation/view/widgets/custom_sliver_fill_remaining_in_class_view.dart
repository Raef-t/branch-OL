import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/many_of_card_and_two_texts_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/sized_boxs/heights.dart';
import '/features/class/presentation/view/widgets/custom_generate_cards_about_student_in_class_view.dart';
import '/features/class/presentation/view/widgets/custom_header_cards_in_class_view.dart';

class CustomSliverFillRemainingInClassView extends StatefulWidget {
  const CustomSliverFillRemainingInClassView({super.key});

  @override
  State<CustomSliverFillRemainingInClassView> createState() =>
      _CustomSliverFillRemainingInClassViewState();
}

class _CustomSliverFillRemainingInClassViewState
    extends State<CustomSliverFillRemainingInClassView> {
  bool isVisible = false;
  int selectedIndex = 0;
  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height13(context: context),
            const CustomHeaderCardsInClassView(),
            Heights.height38(context: context),
            ManyOfCardAndTwoTextsComponent(
              messageOnTap: () {
                setState(() => isVisible = false);
              },
              attendanceOnTap: () {
                setState(() {
                  isVisible = true;
                  selectedIndex = 1;
                });
              },
              paymentOnTap: () {
                setState(() {
                  isVisible = true;
                  selectedIndex = 2;
                });
              },
              workHourOnTap: () {
                setState(() => isVisible = false);
                pushGoRouterHelper(
                  context: context,
                  view: kWorkHoursToBatchViewRouter,
                );
              },
              markOnTap: () {
                setState(() => isVisible = false);
                pushGoRouterHelper(
                  context: context,
                  view: kMarkToBatchViewRouter,
                );
              },
            ),
            Heights.height20(context: context),
            CustomGenerateCardsAboutStudentInClassView(
              isVisible: isVisible,
              selectedIndex: selectedIndex,
            ),
          ],
        ),
      ),
    );
  }
}
